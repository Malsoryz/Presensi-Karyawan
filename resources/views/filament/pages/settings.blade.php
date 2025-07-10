<x-filament-panels::page>
    <x-filament::tabs>
        <x-filament::tabs.item
            :active="$tabState === 'tab1'"
            wire:click="$set('tabState', 'tab1')"
        >
            tab1
        </x-filament::tabs.item>
    </x-filament::tabs>

    <div class="flex flex-col gap-8">
        @foreach ($this->jadwalFormSections() as $form)
            <x-filament::section>
                <x-slot name="heading">
                    {{ $form['heading'] }}
                </x-slot>

                <{{ $form['tag'] }}>
                    {{ $form['content'] }}
                </{{ $form['tag'] }}>
            </x-filament::section>
        @endforeach
    </div>

</x-filament-panels::page>
