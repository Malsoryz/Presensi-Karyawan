<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColumnGroup;

// use App\Infolists\Components\VerticalTabs\Tabs;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('User Forms')
                    ->contained(false)
                    ->tabs([
                        Tab::make('Profile')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama')
                                    ->required()
                                    ->autoFocus(),
                                TextInput::make('email')
                                    ->label('Email')
                                    ->required()
                                    ->email(),
                                DatePicker::make('birth_date')
                                    ->label('Date of birth'),
                                Radio::make('gender')
                                    ->options([
                                        'male' => 'Laki-Laki',
                                        'female' => 'Perempuan',
                                    ])
                                    ->inline()
                                    ->inlineLabel(false),
                                TextInput::make('phone_number')
                                    ->label('No Telepon')
                                    ->numeric()
                                    ->inputMode('tel'),
                                Textarea::make('address')
                                    ->label('Alamat')
                                    ->autosize()
                                    ->disableGrammarly(),
                            ]),
                        Tab::make('Credential')
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password')
                                    ->required()
                                    ->password()
                                    ->revealable()
                                    ->minLength(8)
                                    ->visibleOn('create'),

                                // untuk mengubah password
                                Placeholder::make('Change password')
                                    ->visibleOn('edit'),
                                TextInput::make('password')
                                    ->label('New password')
                                    ->password()
                                    ->revealable()
                                    ->minLength(8)
                                    ->same('password_confirmation')
                                    ->visibleOn('edit'),
                                TextInput::make('password_confirmation')
                                    ->label('Retype password')
                                    ->password()
                                    ->revealable()
                                    ->minLength(8)
                                    ->same('password')
                                    ->visibleOn('edit'),
                            ]),
                        Tab::make('Data Karyawan')
                            ->schema([
                                TextInput::make('jabatan')
                                    ->label('Jabatan'),
                                TextInput::make('departmen')
                                    ->label('Departmen'),
                                DatePicker::make('tanggal_masuk_sebagai_karyawan')
                                    ->label('Tanggal masuk sebagai karyawan'),
                                TextInput::make('rekening_bank')
                                    ->label('Rekening bank'),
                                TextInput::make('gaji_pokok_bulanan')
                                    ->label('Gaji pokok bulanan')
                                    ->numeric(),
                                TextInput::make('tunjangan_kehadiran_harian')
                                    ->label('Tunjangan kehadiran harian')
                                    ->numeric(),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColumnGroup::make('Profil', [
                    TextColumn::make('name')
                        ->label('Nama')
                        ->searchable(),
                    TextColumn::make('email')
                        ->label('Email'),
                    // TextColumn::make('address')
                    //     ->label('Alamat')
                    //     ->wrap(),
                    // TextColumn::make('birth_date')
                    //     ->label('Tanggal lahir')
                    //     ->date(),
                    // TextColumn::make('gender')
                    //     ->label('Jenis kelamin'),
                    // TextColumn::make('phone_number')
                    //     ->label('No Telepon'),
                ]),
            ])
            ->defaultSort('name')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
