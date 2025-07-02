<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresensiResource\Pages;
use App\Filament\Resources\PresensiResource\RelationManagers;
use App\Models\Presensi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class PresensiResource extends Resource
{
    protected static ?string $model = Presensi::class;

    protected static ?string $slug = 'presensi';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Data Presensi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_karyawan')
                    ->label('Nama Karyawan'),
                TextColumn::make('jenis_presensi')
                    ->label('Jenis Presensi'),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->dateTime(),
                TextColumn::make('ip_address')
                    ->label('Alamat Perangkat'),
            ])
            ->defaultSort('nama_karyawan', 'tanggal')
            ->filters([
                SelectFilter::make('jenis_presensi')
                    ->label('Presensi')
                    ->options([
                        'pagi' => 'Pagi',
                        'siang' => 'Siang',
                    ]),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'create' => Pages\CreatePresensi::route('/create'),
            'edit' => Pages\EditPresensi::route('/{record}/edit'),
        ];
    }
}
