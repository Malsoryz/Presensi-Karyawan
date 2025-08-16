@php
    use Filament\Support\Enums\Alignment;
    use Filament\Tables\Actions\HeaderActionsPosition;
@endphp

@props([
    'header' => $header ?? null,
    'description' => $description ?? null,
    'actions' => $this->table->getHeaderActions(),
    'actionsPosition' => HeaderActionsPosition::Adaptive,
])

<div>
    @if ($header || $description)
        <x-filament-tables::header 
            :heading="$header"
            :description="$description"
            :actions-position="$actionsPosition"
            :actions="$actions"
        />
    @endif
    <div 
        class="{{ 
            $header||$description ? 
            "border-t border-gray-200 dark:border-white/10" : 
            null 
        }}"
    >
        <x-filament::tabs 
            :contained="true"
            style="border: none !important;"
        >
            @foreach ($this->tabItem as $label => $tabId)
                <x-filament::tabs.item 
                    :active="$this->activeTab === $tabId"
                    wire:key="{{ $tabId }}"
                    wire:click="setTab('{{ $tabId }}')"
                >
                    {{ $label }}
                </x-filament::tabs.item>
            @endforeach
        </x-filament::tabs>
    </div>
</div>