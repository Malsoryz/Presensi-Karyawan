<?php

namespace App\Livewire\Forms\Config;

use App\Models\Config;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

class Wifi extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(Config::all()->pluck('value', 'name')->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('ssid')
                        ->label('SSID')
                            ->placeholder('Nama jaringan'),
                        TextInput::make('ip_range')
                            ->label('IP Range'),
                        TextInput::make('static_ip_url')
                            ->label('Static IP Url')
                            ->prefix('http://')
                    ])
            ])
            ->statePath('data');
    }

    #[On('save-wifi')]
    public function save(): void
    {
        foreach ($this->data as $name => $value) {
            Config::set($name, $value);
        }
    }

    public function render(): View
    {
        return view('livewire.forms.config.wifi');
    }
}
