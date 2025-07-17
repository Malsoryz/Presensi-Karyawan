<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminResource\Pages;
use App\Filament\Resources\AdminResource\RelationManagers;
use App\Models\Admin;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdminResource extends Resource
{
    protected static ?string $model = Admin::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Users';

    protected static ?string $navigationLabel = 'Admin';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                ->contained(false)
                ->tabs([
                    Tabs\Tab::make('Admin')->label('Profile')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->placeholder('Masukan Nama'),

                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->placeholder('Masukan Email'),

                        Radio::make('gender')
                            ->options([
                                'male'=>'laki-laki',
                                'female'=>'wanita',
                            ])
                            ->inline()
                            ->inlineLabel(false),
                        
                        TextInput::make('telepon')
                            ->label('No. Telepon')
                            ->required()
                            ->numeric()
                            ->inputMode('tel')
                            ->placeholder('Masukkan No. Telepon'),

                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->autosize(),

                        
                    ]),
                    Tabs\Tab::make('kredensial')
                    ->schema([
                        TextInput::make('password')
                            ->label('Password')
                            ->required()
                            ->password()
                            ->revealable()
                            ->minLength('8')
                            ->placeholder('Masukan Password')
                            ->visibleOn('create'),

                            //untuk edit page
                            TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->required()
                            ->minLength('8')
                            ->same('new_password')
                            ->visibleOn('edit'),

                            TextInput::make('new_password')
                            ->label('Retype Password')
                            ->password()
                            ->required()
                            ->minLength('8')
                            ->same('password')
                            ->visibleOn('edit')
                    ])


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

                TextColumn::make('email')
                ->label('Email'),

                TextColumn::make('gender')
                ->label('Gender'),

                TextColumn::make('telepon')
                ->label('No. Telepon'),

                TextColumn::make('alamat')
                ->label('Alamat')
            ])
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
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}
