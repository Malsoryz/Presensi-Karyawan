@props([
    'header' => $header ?? null,
    'description' => $description ?? null,
])

<div>
    <div class="px-6 p-4 flex flex-row gap-x-1">
        <div class="grid gap-y-1">
            @if ($header)
                <x-filament::section.heading>
                    {{ $header }}
                </x-filament::section.heading>
            @endif
            @if ($description)
                <x-filament::section.description>
                    {{ $description }}
                </x-filament::section.description>
            @endif
        </div>
    </div>
    <div class="border-t border-gray-200 dark:border-white/10">
        <div class="w-auto">
            <x-filament::tabs :contained="true">
                @foreach ($this->tabItem as $tabId => $tab)
                    <x-filament::tabs.item 
                        :active="$this->activeTab === $tabId"
                        wire:key="{{ $tab->key }}"
                        wire:click="setTab('{{ $tabId }}')"
                    >
                        {{ $tab->label }}
                    </x-filament::tabs.item>
                @endforeach
            </x-filament::tabs>
        </div>
    </div>
</div>