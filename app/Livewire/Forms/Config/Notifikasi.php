<?php

namespace App\Livewire\Forms\Config;

use App\Models\Config;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Livewire\Attribute\On;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class Notifikasi extends Component implements HasForms
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
                        TextInput::make('trigger_notifikasi_hr')
                            ->label('Trigger notifikasi HR')
                            ->numeric()
                            ->suffix('Hari'),
                        TextInput::make('metode_notifikasi')
                            ->label('Metode notifikasi'),
                        Textarea::make('template_pesan')
                            ->label('Template pesan')
                            ->autosize()
                            ->placeholder('User tidak melakukan presensi...'),
                    ])
            ])
            ->statePath('data');
    }

    #[On('save-notifikasi')]
    public function save(): void
    {
        foreach ($this->data as $name => $value) {
            Config::set($name, $value);
        }
    }

    public function render(): View
    {
        return view('livewire.forms.config.notifikasi');
    }
}
