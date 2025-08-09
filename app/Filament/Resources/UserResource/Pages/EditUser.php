<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\User\Gender;
use App\Enums\User\Role;
use App\Filament\Resources\UserResource;

use App\Livewire\Tables\User\Tunjangan as TunjanganTable;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextArea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\View;

use Filament\Forms\Components\Actions as FormActions;

use Filament\Support\RawJs;

/*-------------------------------------------------------*/
// CATATAN: Pada resources page ini, menggunakan submit
// button custom berupa hanya action saja, yang pastinya
// tidak stabil ataupun sama seperti button dasarnya,
// terkait permasalahan penggunaan table di component
// form 'Livewire::make()' karena table memiliki form nya
// sendiri yang membuat button submit asli dari form edit nya
// tidak bisa digunakan, sehingga menggunakan form action.
/*-------------------------------------------------------*/

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected static string $view = 'filament.resources.user-resource.pages.edit-user';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('User Forms')
                    ->persistTabInQueryString('tab')
                    // ->contained(false)
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
                        Tabs\Tab::make('Credential')
                            ->schema([
                                TextInput::make('password')
                                    ->label('New Password')
                                    ->required(fn ($state) => filled($state))
                                    ->password()
                                    ->revealable()
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->maxLength(255)
                                    ->confirmed(),
                                TextInput::make('password_confirmation')
                                    ->label('Konfirmasi Password')
                                    ->dehydrated(false)
                                    ->required(fn (Get $get) => filled($get('password')))
                                    ->password()
                                    ->revealable()
                                    ->maxLength(255),
                            ]),
                        Tabs\Tab::make('Data Karyawan')
                            ->schema([
                                Select::make('jabatan_id')
                                    ->label('Jabatan')
                                    ->relationship(name: 'jabatan', titleAttribute: 'nama'),
                                Select::make('divisi_id')
                                    ->label('Jabatan')
                                    ->relationship(name: 'divisi', titleAttribute: 'nama'),
                                Select::make('tipe_id')
                                    ->label('Tipe')
                                    ->relationship(name: 'tipe', titleAttribute: 'nama_tipe'),
                                DatePicker::make('tanggal_masuk')
                                    ->label('Tanggal masuk')
                                    ->visibleOn('edit')
                                    ->disabled(),
                                DatePicker::make('tanggal_masuk_sebagai_karyawan')
                                    ->label('Tanggal masuk sebagai karyawan')
                                    ->visibleOn('edit')
                                    ->disabled(),
                                TextInput::make('rekening_bank')
                                    ->label('Rekening bank'),
                                Group::make()
                                    ->relationship('jabatan')
                                    ->schema([
                                        TextInput::make('gaji_pokok_bulanan')
                                            ->label('Gaji Pokok Bulanan')
                                            ->placeholder('i.e: 1.000.000')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->mask(RawJs::make(<<<'JS'
                                                $money($input, ',', '.');
                                            JS))
                                            ->stripCharacters('.')
                                            ->dehydrated(false),
                                        Hidden::make('id'),
                                        Livewire::make(TunjanganTable::class, fn (Get $get) => [
                                            'id' => $get('id'),
                                        ])
                                            ->key('table-tunjangan')
                                            ->hidden(fn (Get $get): bool => (bool) $get('jabatan_id'))
                                    ]),
                            ]),
                        Tabs\Tab::make('Role')
                            ->schema([
                                Select::make('role')
                                    ->label('Role')
                                    ->options(Role::toSelectItem()),
                            ]),
                        ]),
                    FormActions::make([
                        FormActions\Action::make('save-changes')
                            ->label('Save changes')
                            ->button()
                            ->extraAttributes([
                                'type' => 'submit',
                            ]),
                        FormActions\Action::make('cancel')
                            ->label('Cancel')
                            ->button()
                            ->color('gray')
                            ->extraAttributes([
                                'onclick' => "history.back()"
                            ])
                    ])
            ]);
    }
}