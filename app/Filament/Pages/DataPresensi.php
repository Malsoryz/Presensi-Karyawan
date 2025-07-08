<?php

namespace App\Filament\Pages;

use App\Models\Presensi;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class DataPresensi extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.data-presensi';

    public function table(Table $table): Table
    {
        return $table
            ->query(Presensi::getTotalQuery())
            ->columns([
                TextColumn::make('nama_karyawan')
                    ->label('Nama'),
                TextColumn::make('total_masuk')
                    ->label('Masuk'),
                TextColumn::make('total_terlambat')
                    ->label('Terlambat'),
                TextColumn::make('total_ijin')
                    ->label('Ijin'),
                TextColumn::make('total_sakit')
                    ->label('Sakit'),
                TextColumn::make('total_tidak_masuk')
                    ->label('Tidak masuk'),
            ])
            ->filters([

            ])
            ->actions([

            ])
            ->bulkActions([

            ]);
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record->nama_karyawan;
    }
}
