<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConfigResource\Pages;
use App\Filament\Resources\ConfigResource\RelationManagers;
use App\Models\Config;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class ConfigResource extends Resource
{
    protected static ?string $model = Config::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('key')
                    ->required()
                    ->maxLength(255),
                Textarea::make('value')
                    ->columnSpanFull(),
                Select::make('category')
                    ->required()
                    ->options([
                        'others' => 'Others',
                        'sesi_presensi' => 'Sesi Presensi',
                        'wifi' => 'Wi-Fi',
                        'jadwal_kerja' => 'Jadwal kerja',
                        'potongan_gaji' => 'Potongan Gaji',
                        'bonus_gaji' => 'Bonus Gaji',
                        'notifikasi' => 'Notifikasi',
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->searchable(),
                TextColumn::make('value')
                    ->searchable(),
                TextColumn::make('category')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Category')
                    ->options([
                        'others' => 'Others',
                        'sesi_presensi' => 'Sesi Presensi',
                        'wifi' => 'Wi-Fi',
                        'jadwal_kerja' => 'Jadwal kerja',
                        'potongan_gaji' => 'Potongan Gaji',
                        'bonus_gaji' => 'Bonus Gaji',
                        'notifikasi' => 'Notifikasi',
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListConfigs::route('/'),
            'create' => Pages\CreateConfig::route('/create'),
            'edit' => Pages\EditConfig::route('/{record}/edit'),
        ];
    }
}
