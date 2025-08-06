<x-filament-panels::page>
    <x-filament::tabs>
        <template x-data="@js(['tabs' => $this->getPageTabs()])" x-for="tab in tabs">
            <x-filament::tabs.item
                alpine-active="tab.route === window.location.href"
                x-on:click="window.location.href = tab.route"
            >
                <span x-text="tab.label"></span>
            </x-filament::tabs.item>
        </template>
    </x-filament::tabs>

    {{ $this->table }}
</x-filament-panels::page>
