<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\View;

class Master extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.master';

    protected static ?string $navigationGroup = 'Users';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Master')
                    ->contained(false)
                    ->persistTabInQueryString('tab')
                    ->tabs([
                        Tabs\Tab::make('Jabatan')
                            ->id('jabatan')
                            ->schema([
                                View::make('components.tables.user-jabatan'),
                            ]),
                        Tabs\Tab::make('Tipe')
                            ->id('tipe')
                            ->schema([
                                View::make('components.tables.user-tipe'),
                            ]),
                    ]),
            ]);
    }
}
