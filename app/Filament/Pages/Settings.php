<?php

namespace App\Filament\Pages;

use App\Models\Config;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Actions;

use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Placeholder;

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
                Tabs::make('Configuration settings')
                    ->contained(false)
                    ->tabs([
                        Tabs\Tab::make('Jadwal')
                            ->schema([
                                Section::make('Hari kerja')
                                    ->schema([
                                        
                                    ]),
                                Section::make('Presensi')
                                    ->columns([
                                        'default' => 2,
                                    ])
                                    ->schema([
                                        TextInput::make('data.timezone')
                                            ->label('Timezone')
                                            ->columnSpan(['default' => 2]),
                                        TimePicker::make('data.presensi_pagi_mulai')
                                            ->label('Pagi Mulai')
                                            ->native(false)
                                            ->displayFormat('H:i:s')
                                            ->columnSpan(['default' => 1]),
                                        TimePicker::make('data.presensi_pagi_selesai')
                                            ->label('Pagi Selesai')
                                            ->native(false)
                                            ->displayFormat('H:i:s')
                                            ->columnSpan(['default' => 1]),
                                        TimePicker::make('data.presensi_siang_mulai')
                                            ->label('Siang Mulai')
                                            ->native(false)
                                            ->displayFormat('H:i:s')
                                            ->columnSpan(['default' => 1]),
                                        TimePicker::make('data.presensi_siang_selesai')
                                            ->label('Siang Selesai')
                                            ->native(false)
                                            ->displayFormat('H:i:s')
                                            ->columnSpan(['default' => 1]),
                                    ]),
                                Section::make('Jam kerja')
                                    ->columns([
                                        'default' => 2,
                                    ])
                                    ->schema([
                                        TimePicker::make('data.jam_mulai_kerja')
                                            ->label('Kerja mulai')
                                            ->native(false)
                                            ->displayFormat('H:i:s')
                                            ->columnSpan(['default' => 1]),
                                        TimePicker::make('data.jam_selesai_istirahat')
                                            ->label('Selesai istirahat')
                                            ->native(false)
                                            ->displayFormat('H:i:s')
                                            ->columnSpan(['default' => 1]),
                                        TextInput::make('data.toleransi_presensi')
                                            ->numeric()
                                            ->label('Toleransi presensi')
                                            ->suffix('Menit')
                                            ->columnSpan(2),
                                    ])
                            ]),
                        Tabs\Tab::make('Wi-Fi')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextInput::make('data.ssid')
                                            ->label('SSID')
                                            ->placeholder('Nama jaringan'),
                                        TextInput::make('data.ip_range')
                                            ->label('IP Range'),
                                        TextInput::make('data.static_ip_url')
                                            ->label('Static IP Url')
                                            ->prefix('http://')
                                    ])
                            ]),
                        Tabs\Tab::make('Notifikasi')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextInput::make('data.trigger_notifikasi_hr')
                                            ->label('Trigger notifikasi HR')
                                            ->numeric()
                                            ->suffix('Hari'),
                                        TextInput::make('data.metode_notifikasi')
                                            ->label('Metode notifikasi'),
                                        Textarea::make('data.template_pesan')
                                            ->label('Template pesan')
                                            ->autosize()
                                            ->placeholder('User tidak melakukan presensi...'),
                                    ])
                            ]),
                        Tabs\Tab::make('Aturan')
                            ->columns(['default' => 2])
                            ->schema([
                                Section::make()
                                    ->schema([
                                        TextInput::make('data.potongan_tidak_masuk')
                                            ->label('Potongan tidak masuk')
                                            ->numeric()
                                            ->suffix('%'),
                                        TextInput::make('data.potongan_telat')
                                            ->label('Potongan telat')
                                            ->numeric()
                                            ->suffix('% per kejadian'),
                                        TextInput::make('data.threshold_kehadiran_min')
                                            ->label('Threshold kehadiran minimal')
                                            ->numeric()
                                            ->suffix('%'),
                                        TextInput::make('data.ambang_batas_keterlambatan')
                                            ->label('Ambang batas keterlambatan')
                                            ->suffix('Kali')
                                    ])
                            ]),
                        Tabs\Tab::make('Hari libur')
                            ->schema([
                                
                            ])
                    ]),
                Actions::make([
                    Actions\Action::make('save')
                        ->label('Save changes')
                        ->action(function () {
                            foreach ($this->data as $name => $value) {
                                Config::set($name, $value);
                            }
                        })
                        ->requiresConfirmation(),
                ])
            ]);
    }
}
