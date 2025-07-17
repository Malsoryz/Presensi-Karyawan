<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Saved backgrounds
        </x-slot>

        @foreach ($this->getListOfBackgrounds() as $background)
            <div>
                <img src="{{ asset('storage/'.$background->image_path) }}" alt="Background Image" class="w-full h-64 object-cover mb-4">
            </div>
        @endforeach

    </x-filament::section>

    <x-filament-actions::modals/>
</x-filament-panels::page>
