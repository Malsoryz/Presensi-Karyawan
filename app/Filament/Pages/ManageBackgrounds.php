<?php

namespace App\Filament\Pages;

use App\Models\Background;
use App\Models\Config;

use Illuminate\Support\Str;

use Filament\Pages\Page;
use Filament\Forms\Get;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ManageBackgrounds extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.manage-backgrounds';

    public function createAction(): Action
    {
        return Action::make('create')
            ->label('Add Background')
            ->form([
                TextInput::make('name')
                    ->label('Background Name')
                    ->required()
                    ->maxLength(255),
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
