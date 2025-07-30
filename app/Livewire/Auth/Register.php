<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

use App\Enums\User\Gender;
use App\Enums\User\Role;

use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;

class Register extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Profile')
                        ->description('Informasi dasar pengguna')
                        ->columns([
                            'default' => 2
                        ])
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama')
                                ->required()
                                ->columnSpanFull()
                                ->placeholder('Masukkan nama lengkap Anda'),
                            TextInput::make('email')
                                ->label('Email')
                                ->required()
                                ->columnSpanFull()
                                ->email()
                                ->placeholder('Masukkan alamat email Anda'),
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
                                ->placeholder('Masukan nomor telepon anda'),
                                Textarea::make('address')
                                ->label('Alamat')
                                ->required()
                                ->columnSpanFull()
                                ->autosize()
                                ->disableGrammarly()
                                ->placeholder('Masukan alamat anda'),
                        ]),
                    Wizard\Step::make('Credential')
                        ->description('Data penting pengguna')
                        ->schema([
                            Select::make('tipe_id')
                                ->label('Tipe pengguna')
                                ->required()
                                ->relationship(name: 'tipe', titleAttribute: 'nama_tipe'),
                            TextInput::make('password')
                                ->label('Password')
                                ->required()
                                ->password()
                                ->revealable()
                                ->confirmed(),
                            TextInput::make('password_confirmation')
                                ->label('Konfirmasi Password')
                                ->dehydrated(false)
                                ->required()
                                ->password()
                                ->revealable(),
                        ]),
                    Wizard\Step::make('Extra')
                        ->description('Data extra pengguna')
                        ->schema([
                            Checkbox::make('agreement')
                                ->label('Dengan ini saya menyetujui Syarat dan Ketentuan')
                                ->dehydrated(false)
                                ->accepted(),
                        ]),
                ])
                ->contained(false)
                ->submitAction(view('filament::components.button.index', [
                    'slot' => 'Submit',
                    'type' => 'submit',
                ])),
            ])
            ->statePath('data')
            ->model(User::class);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $record = User::create($data);

        $this->form->model($record)->saveRelationships();
    }

    public function render(): View
    {
        return view('livewire.auth.register')->layout('filament-panels::components.layout.base');
    }
}