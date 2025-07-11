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

class Aturan extends Component implements HasForms
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
                        TextInput::make('potongan_tidak_masuk')
                            ->label('Potongan tidak masuk')
                            ->numeric()
                            ->suffix('%'),
                        TextInput::make('potongan_telat')
                            ->label('Potongan telat')
                            ->numeric()
                            ->suffix('% per kejadian'),
                        TextInput::make('threshold_kehadiran_min')
                            ->label('Threshold kehadiran minimal')
                            ->numeric()
                            ->suffix('%'),
                        TextInput::make('ambang_batas_keterlambatan')
                            ->label('Ambang batas keterlambatan')
                            ->suffix('Kali')
                    ])
            ])
            ->statePath('data');
    }

    #[On('save-aturan')]
    public function save(): void
    {
        foreach ($this->data as $name => $value) {
            Config::set($name, $value);
        }
    }

    public function render(): View
    {
        return view('livewire.forms.config.aturan');
    }
}
