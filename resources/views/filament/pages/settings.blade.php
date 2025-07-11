<x-filament-panels::page>
    <x-filament::tabs>
        <x-filament::tabs.item
            :active="$currentTab === 'tab1'"
            wire:click="$set('currentTab', 'tab1')"
        >
            Jadwal
        </x-filament::tabs.item>

        <x-filament::tabs.item
            :active="$currentTab === 'tab2'"
            wire:click="$set('currentTab', 'tab2')"
        >
            Wi-Fi
        </x-filament::tabs.item>

        <x-filament::tabs.item
            :active="$currentTab === 'tab3'"
            wire:click="$set('currentTab', 'tab3')"
        >
            Notifikasi
        </x-filament::tabs.item>

        <x-filament::tabs.item
            :active="$currentTab === 'tab4'"
            wire:click="$set('currentTab', 'tab4')"
        >
            Aturan
        </x-filament::tabs.item>

        <x-filament::tabs.item
            :active="$currentTab === 'tab5'"
            wire:click="$set('currentTab', 'tab5')"
        >
            Hari Libur
        </x-filament::tabs.item>
    </x-filament::tabs>

    <div @class([
        'hidden' => $currentTab !== 'tab1'
    ])>
        @livewire('tables.config.hari-kerja')
        @livewire('forms.config.jam-kerja')
    </div>

    <div @class([
        'hidden' => $currentTab !== 'tab2'
    ])>
         @livewire('forms.config.wifi')
    </div>

    <div @class([
        'hidden' => $currentTab !== 'tab3'
    ])>
        @livewire('forms.config.notifikasi')
    </div>

    <div @class([
        'hidden' => $currentTab !== 'tab4'
    ])>
        @livewire('forms.config.aturan')
    </div>


    <x-filament::button class="max-w-fit" wire:click="saveChanges">
        Save Changes
    </x-filament::button>

</x-filament-panels::page>
