<x-filament-panels::page>
    <div class="flex flex-col gap-4">
        @foreach ($this->getNotifications() as $notif)
            <x-filament::section collapsible>
                <x-slot name="heading">
                    {{ $notif->title }}
                </x-slot>
                <div class="flex flex-col gap-8">
                    <p class="font-normal">
                        {{ $notif->description }}
                    </p>
                    <div class="w-full flex flex-row justify-end gap-2">
                        <x-filament::button color="gray">
                            {{ $this->getNotificationAction()->ignore->label }}
                        </x-filament::button>
                        <x-filament::button wire:click="responseApproveRequest({{ $notif->id }})">
                            {{ $this->getNotificationAction()->approve->label }}
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        @endforeach
    </div>
</x-filament-panels::page>
