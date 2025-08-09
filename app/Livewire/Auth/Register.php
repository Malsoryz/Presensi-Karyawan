<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\Config;
use App\Models\Tipe;
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
use Filament\Forms\Components\FileUpload;

use App\Forms\Components\TermOfServices;
use Filament\Forms\Get;

use Filament\Support\Enums\MaxWidth;

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
        return view('livewire.auth.register')->layout('components.layouts.auth', ['maxWidth' => MaxWidth::FourExtraLarge]);
    }
}