<?php

namespace App\Filament\Pages;

use App\Models\Config;
use App\Models\Background;

use App\Models\PesanStatus;
use App\Models\Quote;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;

use Filament\Pages\Page;
use Filament\Forms\Get;
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

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

use App\Livewire\Tables\Config\HariKerja;
use App\Livewire\Tables\Config\HariLibur;
use App\Livewire\Tables\Presensi\StatusTable;
use App\Livewire\Tables\Presensi\QuotesTable;

use Filament\Forms\Components\Livewire;

use App\Livewire\Grid\ImageSection;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Filament\Notifications\Notification as Notif;

use Carbon\Carbon;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(
            Config::all()->pluck('value', 'name')->toArray(),
        );
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Configuration settings')
                    ->contained(false)
                    ->persistTabInQueryString('tab')
                    ->tabs([
                        ...$this->generalTab(),
                        ...$this->presensiTab(),
                        ...$this->hariKerjaTab(),
                        ...$this->wifiTab(),
                        ...$this->notificationTab(),
                        ...$this->aturanTab(),
                        ...$this->hariLiburTab(),
                        ...$this->onRegisterTab(),
                    ]),
                Actions::make([
                    Actions\Action::make('save')
                        ->label('Save changes')
                        ->action(function () {
                            $appLogoTemp = collect($this->form->getState()['app_logo'])->first();

                            $appLabel = $this->form->getState()['app_brand'] ?? 'app-logo-label';

                            if ($appLogoTemp instanceof TemporaryUploadedFile) {
                                $extension = $appLogoTemp->getClientOriginalExtension();

                                $path = Storage::disk('local')->putFileAs(
                                    'app/general',
                                    new File($appLogoTemp->getPathname()),
                                    "{$appLabel}.{$extension}"
                                );

                                $this->form->getState()['app_logo'] = $path;

                            } else {
                                $this->form->getState()['app_logo'] = $appLogoTemp;
                            }

                            collect($this->form->getState())->each(function ($value, $name) {
                                Config::set($name, $value);
                            });

                            Notif::make()
                                ->title('Perubahan di simpan')
                                ->success()
                                ->send();
                        }),
                    ]),
            ])
            ->statePath('data');
    }

    protected function generalTab(): array
    {
        return [
            Tabs\Tab::make('General')
                ->id('general')
                ->schema([
                    Section::make()
                        ->columns(8)
                        ->schema([
                            FileUpload::make('app_logo')
                                ->label('Logo aplikasi')
                                ->nullable()
                                ->disk('public')
                                ->directory('app/general')
                                ->default(function ($state) {
                                    $path = $state ?: Config::get('app_logo');

                                    return $path ? [[
                                        'name' => basename($path),
                                        'path' => $path,
                                        'url'  => Storage::disk('public')->url($path), // karena FileUpload pakai disk public
                                    ]] : [];
                                })
                                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, Get $get) {
                                    $name = $get('app_brand') ?? 'app logo';
                                    $slug = Str::slug($name, '-');
                                    $extension = $file->getClientOriginalExtension();
                                    return "{$name}.{$extension}";
                                }),
                            TextInput::make('app_brand')
                                ->label('Brand aplikasi')
                                ->columnSpan(7),
                        ])
                ]),
        ];
    }

    protected function presensiTab(): array
    {
        return [
            Tabs\Tab::make('Presensi')
                ->id('presensi')
                ->schema([
                    Tabs::make('Presensi')
                        ->view('filament.components.override.tabs')
                        ->contained(false)
                        ->tabs([
                            Tabs\Tab::make('Backgrounds')
                                ->extraAttributes(['style' => 'margin: 0 !important;'])
                                ->schema([
                                    Livewire::make(ImageSection::class)
                                        ->key('image-section'),
                                ]),
                            Tabs\Tab::make('Waktu')
                                ->extraAttributes(['style' => 'margin: 0 !important;'])
                                ->schema([
                                    Section::make()
                                        ->heading('Waktu')
                                        ->description('Konfigurasi waktu presensi')
                                        ->columns(2)
                                        ->schema([
                                            TimePicker::make('presensi_pagi_mulai')
                                                ->label('Pagi Mulai')
                                                ->native(false)
                                                ->displayFormat('H:i:s')
                                                ->format('H:i:s')
                                                ->columnSpan(1),
                                            TimePicker::make('presensi_pagi_selesai')
                                                ->label('Pagi Selesai')
                                                ->native(false)
                                                ->displayFormat('H:i:s')
                                                ->format('H:i:s')
                                                ->columnSpan(1),
                                            TimePicker::make('presensi_siang_mulai')
                                                ->label('Siang Mulai')
                                                ->native(false)
                                                ->displayFormat('H:i:s')
                                                ->format('H:i:s')
                                                ->columnSpan(1),
                                            TimePicker::make('presensi_siang_selesai')
                                                ->label('Siang Selesai')
                                                ->native(false)
                                                ->displayFormat('H:i:s')
                                                ->format('H:i:s')
                                                ->columnSpan(1),
                                        ]),
                                ]),
                            Tabs\Tab::make('Status')
                                ->columns(1)
                                // ->badge(PesanStatus::all()->count())
                                ->extraAttributes(['style' => 'margin: 0 !important;'])
                                ->schema([
                                    Livewire::make(StatusTable::class)
                                        ->key('status-table'),
                                ]),
                            Tabs\Tab::make('Quote')
                                ->extraAttributes(['style' => 'margin: 0 !important;'])
                                ->schema([
                                    Livewire::make(QuotesTable::class)
                                    ->key('quotes-table'),
                                ]),
                        ]),
                ]),
        ];
    }

    protected function hariKerjaTab(): array
    {
        return [
            Tabs\Tab::make('Hari Kerja')
                ->id('hari-kerja')
                ->schema([
                    Section::make()
                        ->schema([
                            TextInput::make('timezone')
                                ->label('Timezone'),
                        ]),
                    Livewire::make(HariKerja::class)
                        ->key('hari-kerja'),
                    Section::make()
                        ->columns(2)
                        ->schema([
                            TimePicker::make('jam_mulai_kerja')
                                ->label('Kerja mulai')
                                ->native(false)
                                ->displayFormat('H:i:s')
                                ->format('H:i:s')
                                ->columnSpan(1),
                            TimePicker::make('jam_selesai_istirahat')
                                ->label('Selesai istirahat')
                                ->native(false)
                                ->displayFormat('H:i:s')
                                ->format('H:i:s')
                                ->columnSpan(1),
                            TextInput::make('toleransi_presensi')
                                ->numeric()
                                ->label('Toleransi presensi')
                                ->suffix('Menit')
                                ->minValue(0)
                                ->columnSpan(2),
                        ]),
                ]),
        ];
    }

    protected function wifiTab(): array
    {
        return [
            Tabs\Tab::make('Wi-Fi')
                ->id('wifi')
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
                ]),
        ];
    }

    protected function notificationTab(): array 
    {
        return [
            Tabs\Tab::make('Notifikasi')
                ->id('notifikasi')
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
                ]),
        ];
    }

    protected function aturanTab(): array
    {
        return [
            Tabs\Tab::make('Aturan')
                ->id('aturan')
                ->columns(2)
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
                                ->suffix('Kali'),
                            Toggle::make('auto_approve'),
                        ])
                ]),
        ];
    }

    protected function hariLiburTab(): array
    {
        return [
            Tabs\Tab::make('Hari libur')
                ->id('hari-libur')
                ->schema([
                    Livewire::make(HariLibur::class)
                        ->key('hari-libur'),
                ]),
        ];
    }

    protected function onRegisterTab(): array
    {
        return [
            Tabs\Tab::make('On Register')
                ->id('on-register')
                ->schema([
                    Section::make()
                        ->schema([
                            RichEditor::make('short_term_of_service'),
                            TextInput::make('aggrement_label'),
                        ])
                ]),
        ];
    }
}
