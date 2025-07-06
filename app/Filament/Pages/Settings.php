<?php

namespace App\Filament\Pages;

use App\Models\Config;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Actions;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'data' => Config::all()->pluck('value', 'name')->toArray()
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('data.presensi_pagi_mulai')
                    ->label('Presensi Pagi Mulai')
                    ->required(),
                TextInput::make('data.presensi_pagi_selesai')
                    ->label('Presensi Pagi Selesai')
                    ->required(),
                TextInput::make('data.presensi_siang_mulai')
                    ->label('Presensi Siang Mulai')
                    ->required(),
                TextInput::make('data.presensi_siang_selesai')
                    ->label('Presensi Siang Selesai')
                    ->required(),

                Actions::make([
                    Actions\Action::make('save')
                        ->label('Save changes')
                        ->action(function (array $data) {
                            foreach ($this->data as $name => $value) {
                                Config::setConfig($name, $value);
                            }
                        })
                        ->requiresConfirmation(),
                ])
            ]);
    }
}
