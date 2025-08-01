<x-layouts.presensi title="Presensi" x-data="userData" x-init="updateUser()">
    <x-slot name="header">
        <x-presensi.header/>
    </x-slot>
    
    <main class="min-h-screen w-full flex items-center justify-center">

        <div 
            x-data="{
                activeTab: '',
                tabs: [
                    { id: 'tab-presensi', label: 'Presensi' },
                    { id: 'tab-detail', label: 'Detail' },
                ],
            }"
            class="flex flex-col gap-2 min-w-205 max-w-205"
            x-cloak
        >
            <div
                x-init="activeTab = tabs[0].id" 
                class="card glassmorphism p-2 flex flex-row justify-between gap-2"
            >
                <div class="flex flex-row gap-2">
                    <template x-for="tab in tabs" :key="tab.id">
                        <button
                            :class="activeTab === tab.id ? 'bg-base-300/10 hover:bg-base-300/20' : 'bg-transparent hover:bg-base-300/10'"
                            class="btn border-none rounded-lg text-glassmorphism text-white"
                            x-text="tab.label"
                            x-on:click="activeTab = tab.id"
                        ></button>
                    </template>
                </div>
                <div 
                    class="btn border-none bg-transparent text-glassmorphism text-white"
                    x-data="clock"
                    x-init="startTime()"
                    x-text="time"
                >
                    {{-- jam --}}
                    00:00:00
                </div>
            </div>

            <div x-show="activeTab === 'tab-presensi'">
                <div class="flex flex-row gap-2 items-stretch">
                    <div class="flex flex-col gap-2">
                        <div class="card glassmorphism p-2 flex items-center justify-center">
                            <div 
                                x-data="refreshQrCode" 
                                x-init="start()" 
                                x-html="qrCode"
                                class="p-2 bg-white rounded-lg"
                            ></div>
                        </div>
                        <div class="card glassmorphism p-2">
                            {{-- Status --}}
                            <span class="text-glassmorhism text-white text-3xl">
                                Status: <span class="text-green-400">OnTime</span>
                            </span>
                        </div>
                    </div>
                    <div class="card glassmorphism p-8 w-full flex items-center justify-center">
                        {{-- Motivasi --}}
                        <div class="text-glassmorhism text-white text-center flex flex-col gap-8">
                            <p class="break-words text-3xl">“ It is never too late to be what you might have been. ”</p>
                            <span class="font-bold">George Eliot</span>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'tab-detail'">
                <div
                    x-data="presencesData"
                    x-init="refresh()"
                    class="flex flex-col gap-2 w-full"
                >
                    <div class="card glassmorphism flex flex-col overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-glassmorphism text-white">
                                        Akumulasi presensi anda di tahun 2025
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="w-full flex flex-row">
                                <tr class="flex w-full">
                                    <th class="text-glassmorphism text-white">Username</th>
                                    <td class="text-glassmorphism text-white flex-1 flex">
                                        <div class="grid grid-cols-2 gap-x-6 gap-y-4 flex-1">
                                            <div class="col-span-1 flex flex-row justify-between">
                                                <strong>Masuk</strong>
                                                <span x-text="userAccumulation.masuk">10x</span>
                                            </div>
                                            <div class="col-span-1 flex flex-row justify-between">
                                                <strong>Terlambat</strong>
                                                <span x-text="userAccumulation.masuk">10x</span>
                                            </div>
                                            <div class="col-span-1 flex flex-row justify-between">
                                                <strong>Sakit</strong>
                                                <span x-text="userAccumulation.masuk">10x</span>
                                            </div>
                                            <div class="col-span-1 flex flex-row justify-between">
                                                <strong>Ijin</strong>
                                                <span x-text="userAccumulation.masuk">10x</span>
                                            </div>
                                            <div class="col-span-1 flex flex-row justify-between">
                                                <strong>Tidak Masuk</strong>
                                                <span x-text="userAccumulation.masuk">10x</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card glassmorphism">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-glassmorphism text-white">No</th>
                                    <th class="text-glassmorphism text-white">Nama</th>
                                    <th class="text-glassmorphism text-white">Jam presensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= 5; $i++)
                                    <tr>
                                        <td class="text-glassmorphism text-white">{{ $i }}</td>
                                        <td class="text-glassmorphism text-white">Username</td>
                                        <td class="text-glassmorphism text-white">00:00:00</td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

</x-layouts.presensi>