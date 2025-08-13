<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Models\Presensi;

use App\Filament\Resources\PresensiResource\Pages;
use App\Filament\Resources\PresensiResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\Alignment;

class PresensiResource extends Resource
{
    protected static ?string $model = Presensi::class;

    protected static ?string $navigationLabel = 'Data Presensi';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    public static function getNavigationBadge(): ?string
    {
        return User::whereHas('presensis')->count();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Presensi::getTotalQuery())
            ->columns([
                TextColumn::make('nama_karyawan')
                    ->label('Nama'),
                TextColumn::make('total_masuk')
                    ->label('Masuk')
                    ->color('success')
                    ->alignment(Alignment::Center),
                TextColumn::make('total_terlambat')
                    ->label('Terlambat')
                    ->color('gray')
                    ->alignment(Alignment::Center),
                TextColumn::make('total_ijin')
                    ->label('Ijin')
                    ->color('gray')
                    ->alignment(Alignment::Center),
                TextColumn::make('total_sakit')
                    ->label('Sakit')
                    ->color('gray')
                    ->alignment(Alignment::Center),
                TextColumn::make('total_tidak_masuk')
                    ->label('Tidak masuk')
                    ->color('danger')
                    ->alignment(Alignment::Center),
            ])
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPresensis::route('/'),
        ];
    }
}
