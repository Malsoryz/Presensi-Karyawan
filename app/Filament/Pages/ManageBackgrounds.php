<?php

namespace App\Filament\Pages;

use App\Models\Background;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;

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
            ]);
    }

    public function getHeaderActions(): array
    {
        return [
            $this->createAction(),
        ];
    }

}
