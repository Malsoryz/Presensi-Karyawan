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

use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColumnGroup;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('User Forms')
                    ->tabs([
                        Tabs\Tab::make('Profile')
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
                        Tabs\Tab::make('Credential')
                            ->schema([
                                TextInput::make('password')
                                    ->label('Password')
                                    ->required()
                                    ->password()
                                    ->revealable()
                                    ->minLength(8)
                            ])
                    ])
                    ->contained(false),
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
                    TextColumn::make('address')
                        ->label('Alamat')
                        ->wrap(),
                    TextColumn::make('birth_date')
                        ->label('Tanggal lahir')
                        ->date(),
                    TextColumn::make('gender')
                        ->label('Jenis kelamin'),
                    TextColumn::make('phone_number')
                        ->label('No Telepon'),
                ]),
            ])
            ->defaultSort('name')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
