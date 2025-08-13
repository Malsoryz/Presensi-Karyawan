<?php

namespace App\Filament\Resources;

use App\Models\User;
use App\Models\Config;
use App\Models\Tipe;
use App\Models\Jabatan;
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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\FileUpload;

use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;

use App\Forms\Components\TermOfServices;
use App\Livewire\Tables\User\Tunjangan as TunjanganTable;
use Filament\Forms\Get;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Filament\Support\RawJs;

use Filament\Forms\Components\Livewire;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationGroup = 'Users';

    protected static ?string $navigationLabel = 'List';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Wizard::make([
                    /*------------------------------------------PROFILE--------------------------------------------*/
                    Wizard\Step::make('Profile')
                        ->description('Informasi dasar pengguna')
                        ->columns(2)
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama')
                                ->required()
                                ->columnSpanFull()
                                ->placeholder('i.e: Budi Hermawan'),
                            TextInput::make('email')
                                ->label('Email')
                                ->required()
                                ->columnSpanFull()
                                ->email()
                                ->placeholder('i.e: email@example.com'),
                            DatePicker::make('birth_date')
                                ->label('Tanggal lahir')
                                ->required()
                                ->columnSpan(1),
                            Radio::make('gender')
                                ->label('Jenis kelamin')
                                ->required()
                                ->columnSpan(1)
                                ->options([
                                    'laki-laki' => 'Laki-Laki',
                                    'perempuan' => 'Perempuan',
                                ])
                                ->inline()
                                ->inlineLabel(false),
                            TextInput::make('phone_number')
                                ->label('No telepon')
                                ->required()
                                ->columnSpanFull()
                                ->numeric()
                                ->inputMode('tel')
                                ->mask('9999 9999 9999')
                                ->stripCharacters(' ')
                                // ->prefix('+62')
                                ->placeholder('i.e: 0812 3456 7890'),
                            Textarea::make('address')
                                ->label('Alamat')
                                ->required()
                                ->columnSpanFull()
                                ->autosize()
                                ->disableGrammarly()
                                ->placeholder('i.e: Jalan kayutangi 2...'),
                        ]),
                    /*------------------------------------------CREDENTIAL--------------------------------------------*/
                    Wizard\Step::make('Credential')
                        ->description('Data penting pengguna')
                        ->schema([
                            Hidden::make('status_approved')
                                ->default(fn() => (bool) Config::get('auto_approve', false)),
                            Select::make('divisi')
                                ->label('Divisi')
                                ->relationship(name: 'divisi', titleAttribute: 'nama'),
                            Select::make('tipe_id')
                                ->label('Tipe pengguna')
                                ->placeholder('i.e: Karyawan tetap, magang...')
                                ->required()
                                ->relationship(name: 'tipe', titleAttribute: 'nama_tipe'),
                            TextInput::make('password')
                                ->label('Password')
                                ->placeholder('Password')
                                ->required()
                                ->password()
                                ->revealable()
                                ->confirmed(),
                            TextInput::make('password_confirmation')
                                ->label('Konfirmasi Password')
                                ->placeholder('Password')
                                ->dehydrated(false)
                                ->required()
                                ->password()
                                ->revealable(),
                        ]),
                    /*------------------------------------------EXTRAS--------------------------------------------*/
                    Wizard\Step::make('Extra')
                        ->description('Data extra pengguna')
                        ->schema([
                            TermOfServices::make('term_of_services')
                                ->label('Term of Services'),
                            FileUpload::make('surat_pernyataan')
                                ->label('Surat pernyataan')
                                ->required(function (Get $get): bool {
                                    $tipe = Tipe::find($get('tipe_id') ?? 0);
                                    return $tipe ? $tipe->wajib_upload : false;
                                })
                                ->visible(function (Get $get): bool {
                                    $tipe = Tipe::find($get('tipe_id') ?? 0);
                                    return $tipe ? $tipe->wajib_upload : false;
                                })
                                ->image()
                                ->disk('public')
                                ->directory('documents')
                                ->visibility('public')
                                ->maxSize(2048)
                                ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, Get $get): string
                                {
                                    $name =  $get('name');
                                    $timestamp = now(Config::get('timezone', 'Asia/Makassar'))->format('YmdHis');
                                    $extension = $file->getClientOriginalExtension();
                                    return "surat-pernyataan-{$name}-{$timestamp}.{$extension}";
                                }),
                            Checkbox::make('agreement')
                                ->label(fn() => Config::get('aggrement_label', ''))
                                ->dehydrated(false)
                                ->accepted(),
                        ]),
                ])
                ->submitAction(view('filament.partial.submitAction'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('role')
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
