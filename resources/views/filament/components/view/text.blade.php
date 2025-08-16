@props([
    'heading' => null,
    'body' => null
])

@if ($heading)
    <div>
        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">
            {{ $heading }}<sup class="text-danger-600 dark:text-danger-400 font-medium">*</sup>
        </span>
        @if ($body)
            <p>{{ $body }}</p>
        @else
            <p class="px-1">
                Harus menyertakan format ':{status}' agar status sekarang ini bisa di tampilkan.
            </p>
        @endif
    </div>
@endif
