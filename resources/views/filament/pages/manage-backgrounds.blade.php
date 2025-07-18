<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Saved backgrounds
        </x-slot>
        <x-slot name="description">
            Semua background yang tersimpan yang nantinya akan di gunakan untuk halaman presensi.
        </x-slot>
        <x-filament::grid default="4" direction="column">
            <x-filament::grid.column>
                @if ($this->getListOfBackgrounds()->isEmpty())
                    tidak ada image
                @else

                    @foreach ($this->getListOfBackgrounds() as $background)
                        <x-filament::modal 
                            width="{{ \Filament\Support\Enums\MaxWidth::SevenExtraLarge }}"
                            {{-- alignment="{{ \Filament\Support\Enums\Alignment::Center }}" --}}
                        >
                            <x-slot name="trigger">
                                <img src="{{ asset('storage/'.$background->image_path) }}" alt="{{ $background->name }}">
                            </x-slot>

                            <x-slot name="heading">
                                {{ $background->name }}
                            </x-slot>

                            <div class="fi ctn flex flex-row gap-4">
                                <div class="flex-grow"></div>
                                {{ $this->deleteAction($background) }}
                            </div>

                            <img src="{{ asset('storage/'.$background->image_path) }}" alt="{{ $background->name }}">
                        </x-filament::modal>
                    @endforeach

                @endif
            </x-filament::grid.column>
        </x-filament::grid>
    </x-filament::section>

    <x-filament-actions::modals/>
</x-filament-panels::page>
