<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;

use App\Enums\User\Gender;
use App\Enums\User\Role;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Resources\Resource;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Users';

    protected static ?string $navigationLabel = 'List';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('User Forms')
                    // ->contained(false)
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
                                    ->options(Gender::toSelectItem())
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
                                    ->label(fn (string $operation) => $operation === 'create' ? 'Password' : 'New Password')
                                    ->password()
                                    ->required(fn (string $operation) => $operation === 'create')
                                    ->confirmed()
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->maxLength(255)
                                    ->revealable(),

                                TextInput::make('password_confirmation')
                                    ->label('Confirm Password')
                                    ->password()
                                    ->required(fn (string $operation) => $operation === 'create')
                                    ->visible(fn (string $operation) => in_array($operation, ['create', 'edit']))
                                    ->maxLength(255)
                                    ->revealable(),
                            ]),
                        Tab::make('Data Karyawan')
                            ->schema([
                                Select::make('jabatan_id')
                                    ->label('Jabatan')
                                    ->relationship(name: 'jabatan', titleAttribute: 'nama')
                                    ->disabled(fn (string $operation) => $operation === 'edit'),
                                TextInput::make('departmen')
                                    ->label('Departmen'),
                                Select::make('tipe_id')
                                    ->label('Tipe')
                                    ->relationship(name: 'tipe', titleAttribute: 'nama_tipe')
                                    ->disabled(fn (string $operation) => $operation === 'edit'),
                                DatePicker::make('tanggal_masuk')
                                    ->label('Tanggal masuk')
                                    ->visibleOn('edit')
                                    ->disabled(fn (string $operation) => $operation === 'edit'),
                                DatePicker::make('tanggal_masuk_sebagai_karyawan')
                                    ->label('Tanggal masuk sebagai karyawan')
                                    ->visibleOn('edit')
                                    ->disabled(fn (string $operation) => $operation === 'edit'),
                                TextInput::make('rekening_bank')
                                    ->label('Rekening bank'),
                                TextInput::make('gaji_pokok_bulanan')
                                    ->label('Gaji pokok bulanan')
                                    ->numeric()
                                    ->visibleOn('edit')
                                    ->disabled(fn (string $operation) => $operation === 'edit'),
                                TextInput::make('tunjangan_kehadiran_harian')
                                    ->label('Tunjangan kehadiran harian')
                                    ->numeric()
                                    ->visibleOn('edit')
                                    ->disabled(fn (string $operation) => $operation === 'edit'),
                            ]),
                        Tab::make('Role')
                            ->schema([
                                Select::make('role')
                                    ->label('Role')
                                    ->options(Role::toSelectItem()),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('jabatan.nama')
                    ->label('Role'),
                TextColumn::make('email')
                    ->label('Email'),
            ])
            ->defaultSort('name')
            ->filters([
                
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
