@props([
    'type' => null,
    'message' => null,
    'secondDuration' => 5,
])

@php
    use App\Enums\Alert;

    $iconType = match ($type) {
        'success', Alert::Success => Alert::Success,
        'danger', Alert::Danger => Alert::Danger,
        'error', Alert::Error => Alert::Error,
        'info', 'information', Alert::Info => Alert::Info,
        null, 'neutral', 'netral', 'default', Alert::Neutral => Alert::Neutral,
        default => null,
    };

    $duration = (int) $secondDuration * 1000;
@endphp

@if ($message)
    <template
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, {{ $duration }})"
        x-if="show"
    >
        <div 
            {{ $attributes->class([
                'alert',
                'glassmorphism',
                'text-glassmorphism',
                'text-white',
                $iconType->getClass(),
            ]) }}
            role="alert"
        >
            {!! $iconType->getIcon() !!}
            <span class="text-sm md:text-lg">
                {{ $message }}
            </span>
        </div>
    </template>
@endif

