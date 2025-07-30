<div class="w-full min-h-screen flex items-center justify-center">
    <x-filament::section>
        <div class="flex flex-col items-center justify-center gap-8">
            <x-filament-panels::header.simple
                heading="Register"
                subheading="Register akun baru."
            />
            <form wire:submit="create">
                {{ $this->form }}
            </form>
            <x-filament-actions::modals />
        </div>
        <footer class="w-full flex justify-center text-xs">
            <span>
                Sudah punya akun? <x-filament::link size="sm" weight="bold" color="info">Login.</x-filament::link>
            </span>
        </footer>
    </x-filament::section>
</div>

@push('scripts')
<script>

console.log('Hello world!');

</script>
@endpush