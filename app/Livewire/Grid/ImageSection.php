<?php

namespace App\Livewire\Grid;

use App\Models\Background;
use App\Models\Config;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\View\View;
use Livewire\Component;

use Filament\Tables\Table;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Filters\TernaryFilter;

use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Checkbox;

use Filament\Notifications\Notification;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImageSection extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(Background::query())
            ->columns([
                Stack::make([
                    ViewColumn::make('id')
                        ->view('filament.components.view.background')
                        ->action(EditAction::make('view-edit')
                            ->form([
                                TextInput::make('name')
                                    ->label('Background name'),
                                Checkbox::make('special_friday')
                                    ->label('Untuk jum \'at'),
                                ViewField::make('image_path')
                                    ->view('filament.components.view.background')
                                    ->dehydrated(false),
                            ])
                            ->extraModalFooterActions([
                                DeleteAction::make()
                                    ->requiresConfirmation()
                                    ->modalIcon('heroicon-o-trash')
                                    ->modalHeading('Delete background?')
                                    ->modalDescription('Are you sure you\'d like to delete this background? This cannot be undone.')
                                    ->cancelParentActions('view-edit')
                                    ->action(function (Background $record) {
                                        $image = $record->image_path;
                                        if ($image && Storage::disk('public')->exists($image)) {
                                            Storage::disk('public')->delete($image);
                                        }

                                        Background::where('id', $record->id)->delete();
                                    }),
                            ]),
                        ),
                ]),
            ])
            ->contentGrid([
                'default' => 4,
            ])
            ->heading('Background')
            ->description('Latar belakang presensi.')
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah')
                    ->modalHeading('Tambah background baru')
                    ->form([
                        TextInput::make('name')
                            ->label('Background Name')
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
                                $name = $get('name') ?? $file->getClientOriginalName();
                                $slug = Str::slug($name, '-');
                                $timestamp = now(Config::timezone())->format('YmdHis');
                                $extension = $file->getClientOriginalExtension();
                                return "{$slug}-{$timestamp}.{$extension}";
                            }),
                    ])
                    ->action(function (array $data, Background $background) {
                        $data['name'] ??= pathinfo($data['image_path'])['filename'];
                        $background->create($data);
                        Notification::make()
                            ->title('Image successfully added.')
                            ->success()
                            ->send();
                    }),
            ])
            ->filters([
                TernaryFilter::make('special_friday')
                    ->label('Hari')
                    ->placeholder('Hari')
                    ->trueLabel('Spesial jum \'at')
                    ->falseLabel('Normal'),
            ])
            ->paginated([12, 24, 36, 'all'])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function render()
    {
        return view('livewire.grid.image-section');
    }
}
