<?php

namespace App\Filament\Pages;

use App\Models\Presensi;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;

class DataPresensi extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.data-presensi';

    public ?array $data = [];

    public function table(Table $table): Table
    {
        return $table
            ->query(Presensi::query())
            ->columns([
                TextColumn::make('data.name')
                    ->label('Nama'),
                TextColumn::make('data.masuk')
                    ->label('Masuk'),
                TextColumn::make('data.terlambat')
                    ->label('Terlambat'),
                TextColumn::make('data.ijin')
                    ->label('Ijin'),
                TextColumn::make('data.sakit')
                    ->label('Sakit'),
                TextColumn::make('data.tidak_masuk')
                    ->label('Tidak masuk'),
            ])
            ->filters([

            ])
            ->actions([

            ])
            ->bulkActions([

            ]);
    }
}
