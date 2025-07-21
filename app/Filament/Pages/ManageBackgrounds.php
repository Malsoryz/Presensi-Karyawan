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

use Filament\Forms\Get;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Checkbox;

use Illuminate\Database\Eloquent\Model;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ManageBackgrounds extends Page implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.manage-backgrounds';

    public ?int $selectedId = null;

    public ?Maxwidth $viewModalWidth = MaxWidth::FiveExtraLarge;

    public function openViewModal(int $id)
    {
        $this->selectedId = $id;
        $this->dispatch('open-modal', id: 'view-modal');
    }

    public function createAction(): Action
    {
        return Action::make('create')
            ->label('Add Background')
            ->form([
                TextInput::make('name')
                    ->label('Background Name')
                    ->maxLength(255)
                    ->dehydrated(false),
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
                        $name = $get('name') ?? $file->getClientOriginalName();
                        $slug = Str::slug($name, '-');
                        $timestamp = now(Config::get('timezone', 'Asia/Makassar'))->format('YmdHis');
                        $extension = $file->getClientOriginalExtension();
                        return "{$slug}-{$timestamp}.{$extension}";
                    }),
            ])
            ->action(function (array $data, Background $background) {
                $background->create($data);
                Notification::make()
                    ->title('Image successfully added.')
                    ->success()
                    ->send();
            });
    }

    public function deleteAction(): Action
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
                $background = Background::find($this->selectedId);
                if ($background->image_path && Storage::disk('public')->exists($background->image_path)) {
                    Storage::disk('public')->delete($background->image_path);
                }

                Background::where('id', $background->id)->delete();

                Notification::make()
                    ->title('Deleted successfully.')
                    ->success()
                    ->send();

                    $this->selectedId = null;
                    $this->dispatch('close-modal', id: 'view-modal');
            });
    }

    public function getListOfBackgrounds()
    {
        return Background::all();
    }

    public function getBackgrounds($isSpecial = false)
    {
        return Background::where('special_friday', $isSpecial)->get();
    }

    public function findBackground($id)
    {
        return Background::find($id);
    }

    public function getHeaderActions(): array
    {
        return [
            $this->createAction(),
        ];
    }

}
