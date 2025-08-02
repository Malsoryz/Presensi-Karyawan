<div>
    <div class="flex flex-col items-center justify-center gap-8">
        <x-filament-panels::header.simple
            heading="Login ke akun mu."
            subheading="Masukan email dan password mu untuk Log in."
        />
        <form wire:submit="login" class="w-full flex flex-col gap-8">
            {{ $this->form }}
            <x-filament::button class="w-full" type="submit">
                Log in
            </x-filament::button>
        </form>
        <x-filament-actions::modals />
    </div>
    <footer class="w-full flex justify-center text-xs">
        <span>
            Belum punya akun? <x-filament::link href="{{ route('register') }}" size="sm" weight="bold" color="info">Register.</x-filament::link>
        </span>
    </footer>
</div>
