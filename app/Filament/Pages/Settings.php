<?php

namespace App\Filament\Pages;

use App\Models\Config;
use App\Models\Background;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;

use Filament\Pages\Page;
use Filament\Forms\Form;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;

use App\Livewire\Tables\Config\HariKerja;
use App\Livewire\Tables\Config\HariLibur;

use App\Forms\Components\ViewLivewire;

use App\Livewire\Grid\ImageSection;

use Filament\Notifications\Notification as Notif;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'data' => Config::all()->pluck('value', 'name')->toArray(),
        ]);
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Configuration settings')
                    ->contained(false)
                    ->persistTabInQueryString('tab')
                    ->tabs([
                        Tabs\Tab::make('Presensi')
                            ->id('presensi')
                            ->schema([
                                ViewLivewire::make(ImageSection::class),
                            ]),
                        Tabs\Tab::make('Hari Kerja')
                            ->id('hari-kerja')
                            ->schema([
                                ViewLivewire::make(HariKerja::class),
                                Section::make()
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
                                Section::make()
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
                                            ->minValue(0)
                                            ->columnSpan(2),
                                    ]),
                            ]),
                        Tabs\Tab::make('Wi-Fi')
                            ->id('wifi')
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
                            ->id('notifikasi')
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
                            ->id('aturan')
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
                                            ->suffix('Kali'),
                                        Toggle::make('data.auto_approve'),
                                    ])
                            ]),
                        Tabs\Tab::make('Hari libur')
                            ->id('hari-libur')
                            ->schema([
                                ViewLivewire::make(HariLibur::class),
                            ]),
                        Tabs\Tab::make('On Register')
                            ->id('on-register')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        RichEditor::make('data.short_term_of_service'),
                                        TextInput::make('data.aggrement_label'),
                                    ])
                            ]),
                    ]),
                Actions::make([
                    Actions\Action::make('save')
                        ->label('Save changes')
                        ->action(function () {
                            foreach ($this->data as $name => $value) {
                                Config::set($name, $value);
                            }
                            Notif::make()
                                ->title('Perubahan di simpan')
                                ->success()
                                ->send();
                        }),
                    ]),
            ]);
        }
}
