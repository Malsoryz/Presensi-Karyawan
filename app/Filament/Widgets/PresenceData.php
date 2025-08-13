<?php

namespace App\Filament\Widgets;

use App\Models\Presensi;
use App\Models\Config;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\TextColumn;

use Filament\Support\Enums\Alignment;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class PresenceData extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public array $tabItem = [];

    public string $activeTab = 'bulan';

    public function mount()
    {
        $this->tabItem = [
            'bulan' => (object) [
                'key' => 'tab-bulan',
                'label' => 'Bulan ini',
            ],
            'tahun' => (object) [
                'key' => 'tab-tahun',
                'label' => 'Tahun ini',
            ],
        ];
    }

    public function getTableQuery(): Builder|Relation
    {
        return match ($this->activeTab) {
            'bulan' => Presensi::getThisMonth(),
            'tahun' => Presensi::getThisYear(),
            default => Presensi::getThisMonth(),
        };
    }

    public function setTab(string $tabId)
    {
        $this->activeTab = $tabId;
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        $activeTab = $this->activeTab;
        $now = now(Config::timezone())->locale('Id');
        return $table
            ->query($this->getTableQuery())
            ->header(view('filament.partial.widget.presence-data.header', [
                'header' => 'Data Presensi',
            ]))
            ->columns([
                TextColumn::make('nama_karyawan'),
                ColumnGroup::make(function () use ($now, $activeTab) {
                    return match ($activeTab) {
                        'bulan' => "Bulan ini ({$now->translatedFormat('F')})",
                        'tahun' => "Akumulasi tahun ini ({$now->year})",
                        default => null,
                    };
                }, [
                    TextColumn::make('total_masuk')
                        ->label('Masuk')
                        ->color('success')
                        ->alignment(Alignment::Center)
                        ->grow(false),
                    TextColumn::make('total_terlambat')
                        ->label('Terlambat')
                        ->alignment(Alignment::Center)
                        ->grow(false),
                    TextColumn::make('total_ijin')
                        ->label('Ijin')
                        ->alignment(Alignment::Center)
                        ->grow(false),
                    TextColumn::make('total_sakit')
                        ->label('Sakit')
                        ->alignment(Alignment::Center)
                        ->grow(false),
                    TextColumn::make('total_tidak_masuk')
                        ->label('Tidak masuk')
                        ->color('danger')
                        ->alignment(Alignment::Center)
                        ->grow(false),
                ])->alignment(Alignment::Center),
            ]);
    }
}