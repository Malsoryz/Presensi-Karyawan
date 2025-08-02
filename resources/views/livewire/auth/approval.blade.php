<div x-data="approval({{ $this->id }})" x-bind="approvalDom">
    <div class="flex w-full flex-col gap-2 text-center">
        <h1 class="text-xl font-medium dark:text-zinc-200" x-text="isApproved ? 'Akun anda telah di approve.' : 'Menunggu admin approve akun anda'"></h1>
        <p class="text-center text-sm dark:text-zinc-400" x-text="isApproved ? 'sebentar lagi anda akan di alihkan ke login' : 'sedang menunggu...'"></p>
    </div>
</div>

@push('scripts')
    @vite(['resources/js/filament.js'])
@endpush