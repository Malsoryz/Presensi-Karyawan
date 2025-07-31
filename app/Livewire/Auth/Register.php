<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Config;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

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
use Filament\Forms\Components\Hidden;

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
                    Wizard\Step::make('Credential')
                        ->description('Data penting pengguna')
                        ->schema([
                            Hidden::make('status_approved')
                                ->default(fn() => (bool) Config::get('auto_approve', false)),
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
                                ->label(fn() => Config::get('aggrement_label', ''))
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

    public function register(): void
    {
        $data = $this->form->getState();
        $user = User::create($data);

        $this->form->model($user)->saveRelationships();

        if (!$data['status_approved']) {
            $user->notifications()->create([
                'title' => "Approval request",
                'description' => "Approval needed for new account '{$user->name}'",
                'type' => 'approval',
            ]);
            $this->redirect(route('approval.wait', ['id' => $user->id]));
        } else {
            Auth::login($user);
            $this->redirect(route('presensi.index'));
        };
    }

    public function render(): View
    {
        return view('livewire.auth.register')->layout('filament-panels::components.layout.base');
    }
}