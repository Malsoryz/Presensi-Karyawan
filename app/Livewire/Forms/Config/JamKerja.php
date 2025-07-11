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
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;

class JamKerja extends Component implements HasForms
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
                    ->columns([
                        'default' => 2,
                        ])
                    ->schema([
                        TextInput::make('timezone')
                            ->label('Timezone')
                            ->columnSpan(['default' => 2]),
                        TimePicker::make('presensi_pagi_mulai')
                            ->label('Pagi Mulai')
                            ->native(false)
                            ->displayFormat('H:i:s')
                            ->columnSpan(['default' => 1]),
                        TimePicker::make('presensi_pagi_selesai')
                            ->label('Pagi Selesai')
                            ->native(false)
                            ->displayFormat('H:i:s')
                            ->columnSpan(['default' => 1]),
                        TimePicker::make('presensi_siang_mulai')
                            ->label('Siang Mulai')
                            ->native(false)
                            ->displayFormat('H:i:s')
                            ->columnSpan(['default' => 1]),
                        TimePicker::make('presensi_siang_selesai')
                            ->label('Siang Selesai')
                            ->native(false)
                            ->displayFormat('H:i:s')
                            ->columnSpan(['default' => 1]),
                    ]),
                Section::make()
                    ->columns([
                        'default' => 2,
                        ])
                    ->schema([
                        TimePicker::make('jam_mulai_kerja')
                            ->label('Kerja mulai')
                            ->native(false)
                            ->displayFormat('H:i:s')
                            ->columnSpan(['default' => 1]),
                        TimePicker::make('jam_selesai_istirahat')
                            ->label('Selesai istirahat')
                            ->native(false)
                            ->displayFormat('H:i:s')
                            ->columnSpan(['default' => 1]),
                        TextInput::make('toleransi_presensi')
                            ->numeric()
                            ->label('Toleransi presensi (menit)')
                            ->columnSpan(2),
                    ]),
            ])
            ->statePath('data');
    }

    #[On('save-jam-kerja')]
    public function save(): void
    {
        foreach ($this->data as $name => $value) {
            Config::set($name, $value);
        }
    }

    public function render(): View
    {
        return view('livewire.forms.config.jam-kerja');
    }
}
