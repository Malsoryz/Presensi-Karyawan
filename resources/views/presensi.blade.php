<x-presensi title="Presensi">
    <x-slot name="header">
        <x-presensi.header/>
    </x-slot>
    
    <main class="min-h-screen w-full flex items-center justify-center">

        <div x-data="presensiTabs" class="flex flex-col gap-4">
            <div class="card glassmorphism p-0.5 flex flex-row gap-1">
                <template x-for="tab in tabs" :key="tab.id">
                    <button
                        :class="activeTab === tab.id ? 'bg-base-300/10' : 'bg-transparent'"
                        class="btn rounded-xl border-none card glassmorphism-text text-white"
                        x-text="tab.label"
                        x-on:click="activeTab = tab.id"
                    ></button>
                </template>
            </div>

            <div class="flex flex-row gap-4 items-stretch">
                <div class="flex flex-col gap-4">
                    <div class="card glassmorphism w-72 h-32">
                        {{-- Jam dinamis --}}
                    </div>
                    <div class="card glassmorphism w-72 h-72">
                        {{-- QRcode --}}
                    </div>
                    <div class="card glassmorphism w-72 h-32">
                        {{-- Status --}}
                    </div>
                </div>
                <div class="card glassmorphism w-128">
                    {{-- Motivasi --}}
                </div>
            </div>
        </div>
    </main>

</x-presensi>

{{-- <div class="flex flex-col gap-4">
    <div class="card glassmorphism w-128 h-64">
        Akumulasi
    </div>
    <div class="card glassmorphism w-128 h-64">
        daftar yang sudah presensi
    </div>
</div> --}}