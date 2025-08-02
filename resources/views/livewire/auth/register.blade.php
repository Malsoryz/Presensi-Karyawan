<div>
    <div class="flex flex-col items-center justify-center gap-8 w-full">
        <x-filament-panels::header.simple
            heading="Register"
            subheading="Register akun baru."
        />
        <form wire:submit="register" class="w-full">
            {{ $this->form }}
        </form>
        <x-filament-actions::modals />
    </div>
    <footer class="w-full flex justify-center text-xs">
        <span>
            Sudah punya akun? <x-filament::link href="{{ route('login') }}" size="sm" weight="bold" color="info">Login.</x-filament::link>
        </span>
    </footer>
</div>