<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Actions\Action;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends Component implements HasForms
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
                TextInput::make('email')
                    ->label('Alamat Email')
                    ->required()
                    ->placeholder('email@example.com')
                    ->email(),
                TextInput::make('password')
                    ->label('Password')
                    ->required()
                    ->placeholder('Password')
                    ->password()
                    ->revealable()
                    ->hintAction(
                        Action::make('lupa_password?'),
                    ),
            ])
            ->statePath('data')
            ->model(User::class);
    }

    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        $email = $this->data['email'] ?? '';
        $password = $this->data['password'] ?? '';
        $remember = $this->data['remember'] ?? false;

        if (!Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            RateLimiter::hit($this->throttleKey($email));

            throw ValidationException::withMessages([
                'data.email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($email));
        Session::regenerate();

        if (Auth::user()->isAdmin()) {
            $this->redirectIntended(route('filament.admin.pages.dashboard'));
        } else $this->redirectIntended(route('presensi.index'));
    }

    protected function ensureIsNotRateLimited(string $email = null): void
    {
        $email = $email ?? ($this->data['email'] ?? '');

        if (!RateLimiter::tooManyAttempts($this->throttleKey($email), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey($email));

        throw ValidationException::withMessages([
            'data.email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(?string $email): string
    {
        return Str::transliterate(Str::lower($email) . '|' . request()->ip());
    }

    public function render(): View
    {
        return view('livewire.auth.login')->layout('components.layouts.auth');
    }
}