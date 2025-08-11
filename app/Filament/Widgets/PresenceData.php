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

class PresenceData extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $now = now(Config::timezone())->locale('Id');
        return $table
            ->heading("Data presensi")
            ->query(Presensi::getThisMonthAndYearAccumulation())
            ->columns([
                TextColumn::make('nama_karyawan'),
                ColumnGroup::make(function () use ($now) {
                    return "Bulan ini ({$now->translatedFormat('F')})";
                }, [
                    TextColumn::make('total_masuk_bulan_ini')
                        ->label('Masuk')
                        ->color('success')
                        ->alignment(Alignment::Center)
                        ->grow(false),
                    TextColumn::make('total_terlambat_bulan_ini')
                        ->label('Terlambat')
                        ->alignment(Alignment::Center)
                        ->grow(false),
                    TextColumn::make('total_ijin_bulan_ini')
                        ->label('Ijin')
                        ->alignment(Alignment::Center)
                        ->grow(false),
                    TextColumn::make('total_sakit_bulan_ini')
                        ->label('Sakit')
                        ->alignment(Alignment::Center)
                        ->grow(false),
                    TextColumn::make('total_tidak_masuk_bulan_ini')
                        ->label('Tidak masuk')
                        ->color('danger')
                        ->alignment(Alignment::Center)
                        ->grow(false),
                ])->alignment(Alignment::Center),
                ColumnGroup::make(function () use ($now) {
                    return "Akumulasi tahun ini ({$now->year})";
                }, [
                    TextColumn::make('total_masuk_tahun_ini')
                        ->label('Masuk')
                        ->color('success')
                        ->alignment(Alignment::Center)
                        ->grow(false),
                    TextColumn::make('total_terlambat_tahun_ini')
                        ->label('Terlambat')
                        ->alignment(Alignment::Center)
                        ->grow(false),
                    TextColumn::make('total_ijin_tahun_ini')
                        ->label('Ijin')
                        ->alignment(Alignment::Center)
                        ->grow(false),
                    TextColumn::make('total_sakit_tahun_ini')
                        ->label('Sakit')
                        ->alignment(Alignment::Center)
                        ->grow(false),
                    TextColumn::make('total_tidak_masuk_tahun_ini')
                        ->label('Tidak masuk')
                        ->color('danger')
                        ->alignment(Alignment::Center)
                        ->grow(false),
                ])->alignment(Alignment::Center),
            ]);
    }
}