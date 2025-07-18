<?php

namespace App\Filament\Pages;

use App\Models\Background;
use App\Models\Config;

use Filament\Support\Enums\MaxWidth;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Filament\Notifications\Notification;

use Filament\Pages\Page;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;

use Filament\Tables\Table;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ViewColumn;

use Filament\Forms\Get;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Checkbox;

use Illuminate\Database\Eloquent\Model;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ManageBackgrounds extends Page implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.manage-backgrounds';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Backgrounds')
            ->description('List dari background yang tersimpan.')
            ->paginated(false)
            ->query(Background::query())
            ->columns([
                Stack::make([
                    ViewColumn::make('image_path')
                        ->view('filament.components.tables.columns.image-column')
                        ->action(
                            TableAction::make('view')
                                ->modalSubmitAction(false)
                                ->modalHeading(fn (Model $record) => 'View ('.$record->name.')')
                                ->modalContent(function (Model $record) {
                                    return view('filament.components.tables.actions.image-modal-content', [
                                        'imagePath' => $record->image_path,
                                        'name' => $record->name,
                                    ]);
                                }),
                        ),
                ]),
            ])
            ->contentGrid([
                'default' => 4,
            ]);
    }

    public function createAction(): Action
    {
        return Action::make('create')
            ->label('Add Background')
            ->form([
                TextInput::make('name')
                    ->label('Background Name')
                    ->required()
                    ->maxLength(255),
                Checkbox::make('special_friday')
                    ->label("Spesial jum'at"),
                FileUpload::make('image_path')
                    ->label('Background Image')
                    ->required()
                    ->image()
                    ->disk('public')
                    ->directory('backgrounds')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, Get $get): string{
                        $name = $get('name') ?? 'background';
                        $slug = Str::slug($name, '-');
                        $timestamp = now(Config::get('timezone', 'Asia/Makassar'))->format('YmdHis');
                        $extension = $file->getClientOriginalExtension();
                        return "{$slug}-{$timestamp}.{$extension}";
                    }),
            ])
            ->action(function (array $data, Background $background) {
                $background->create($data);
                $this->dispatch('backgroundAdded', [
                    'message' => 'Background added successfully!',
                ]);
            });
    }

    public function deleteAction(Background $data): Action
    {
        return Action::make('delete')
            ->label('Delete')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalIcon('heroicon-o-trash')
            ->modalHeading('Delete background?')
            ->modalDescription('Are you sure you\'d like to delete this background? This cannot be undone.')
            ->action(function () {
                if ($data->image_path && Storage::disk('public')->exists($data->image_path)) {
                    Storage::disk('public')->delete($data->image_path);
                }

                Background::where('name', $data->name)->delete();

                Notification::make()
                    ->title('Deleted successfully.')
                    ->success()
                    ->send();
            });
    }

    public function getListOfBackgrounds()
    {
        return Background::all();
    }

    public function getHeaderActions(): array
    {
        return [
            $this->createAction(),
        ];
    }

}
