<x-filament-panels::page>

    <x-filament::section collapsible>
        <x-slot name="heading">
            Backgrounds
        </x-slot>
        <x-slot name="description">
            List dari background yang tersimpan.
        </x-slot>

        <x-filament::grid default="{{ $this->getBackgrounds()->isEmpty() ? 1 : 4 }}" direction="column">
            @if ($this->getBackgrounds()->isEmpty())
                <div class="w-full h-auto flex flex-col justify-center items-center">
                    <x-filament::icon
                        icon="heroicon-o-photo"
                        class="text-gray-700 h-32 w-32"
                    />
                    <span class="text-gray-700">
                        Tidak ada background.
                    </span>
                </div>
            @else
                @foreach ($this->getBackgrounds() as $background)
                    <x-filament::grid.column>
                        <img 
                            src="{{ asset('storage/'.$background->image_path) }}" 
                            alt="{{ $background->name }}"
                            wire:click="openViewModal({{ $background->id }})"
                        >
                    </x-filament::grid.column>
                @endforeach
            @endif
        </x-filament::grid>
    </x-filament::section>

    <x-filament::section collapsible>
        <x-slot name="heading">
            Special Friday Backgrounds
        </x-slot>
        <x-slot name="description">
            Background spesial untuk jum'at.
        </x-slot>

        <x-filament::grid default="{{ $this->getBackgrounds(true)->isEmpty() ? 1 : 4 }}" direction="column">
            @if ($this->getBackgrounds(true)->isEmpty())
                <div class="w-full h-auto flex flex-col justify-center items-center">
                    <x-filament::icon
                        icon="heroicon-o-photo"
                        class="text-gray-700 h-32 w-32"
                    />
                    <span class="text-gray-700">
                        Tidak ada background untuk jum'at.
                    </span>
                </div>
            @else
                @foreach ($this->getBackgrounds(true) as $background)
                    <x-filament::grid.column>
                        <img 
                            src="{{ asset('storage/'.$background->image_path) }}" 
                            alt="{{ $background->name }}"
                            wire:click="openViewModal({{ $background->id }})"
                        >
                    </x-filament::grid.column>
                @endforeach
            @endif
        </x-filament::grid>
    </x-filament::section>

    @php
        $background = $this->findBackground($this->selectedId);
    @endphp
    <x-filament::modal
        id="view-modal"
        width="{{ $this->viewModalWidth }}"
    >
        <x-slot name="heading">
            View ({{ $this->selectedId !== null ? basename($background->image_path) : null }})
        </x-slot>

        <div class="w-full flex flex-col gap-4">
            <div class="w-full flex flex-row-reverse">
                {{ $this->deleteAction() }}
            </div>
            @if ($this->selectedId !== null)
                <img 
                    src="{{ asset('storage/'.$background->image_path) }}" 
                    alt="{{ $background->name }}"
                    class="w-full"
                >
            @endif
        </div>
    </x-filament::modal>

    <x-filament-actions::modals/>
</x-filament-panels::page>
