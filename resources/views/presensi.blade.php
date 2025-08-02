<x-layouts.presensi 
    title="Presensi"
    :background="$background"
    x-data="userData" 
    x-init="updateUser()"
>
    <x-slot name="header">
        <div class="navbar px-8 py-4 w-full fixed top-0 left-0 right-0">
            <div class="navbar-start">
                <template x-if="!$x.isLogin">
                    <button 
                        class="btn glassmorphism text-glassmorphism text-white"
                        x-on:click="window.location.href='{{ route('login') }}'"
                    >
                        Log In
                    </button>
                </template>
                <template x-if="$x.isLogin">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn glassmorphism text-glassmorphism text-white">
                            Log Out
                        </button>
                    </form>
                </template>
            </div>
            <div class="navbar-end">
                <template x-if="$x.isDetected">
                    <span class="btn glassmorphism text-glassmorphism text-white" x-text="$x.user?.name"></span>
                </template>
            </div>
        </div>
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
                    class="btn border-none bg-transparent text-glassmorphism text-white flex gap-2"
                    x-data="datetime"
                >
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-calendar class="text-yellow-400 h-6 w-6"/>
                        <span
                            class="text-yellow-400"
                        ></span>
                    </div>
                    {{-- jam --}}
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-clock class="text-yellow-400 h-6 w-6"/>
                        <span 
                            class="text-yellow-400"
                            x-bind="clockDom"
                        ></span>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'tab-presensi'">
                <div class="flex flex-row gap-2 items-stretch">
                    @if ($isPresenceAllowed)
                        <div class="flex flex-col gap-2">
                            <div class="card glassmorphism p-2 flex items-center justify-center">
                                <div 
                                    x-data="refreshQrCode" 
                                    x-bind="qrDom"
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
                    @endif
                    <div class="card glassmorphism p-8 w-full flex items-center justify-center">
                        {{-- Motivasi --}}
                        <div 
                            class="text-glassmorhism text-white text-center flex flex-col gap-8"
                            x-data="motivation"
                            x-init="start()"
                        >
                            <p 
                                class="break-words text-3xl"
                                x-text="words"
                            ></p>
                            <span class="font-bold" x-text="author"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div x-show="activeTab === 'tab-detail'">
                <div
                    x-data="presencesData"
                    x-bind="detailDom"
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
                                    <th class="text-glassmorphism text-white" x-text="$x.user?.name"></th>
                                    <td class="text-glassmorphism text-white flex-1 flex">
                                        <div class="grid grid-cols-2 gap-x-6 gap-y-4 flex-1">
                                            <div class="col-span-1 flex flex-row justify-between">
                                                <strong>Masuk</strong>
                                                <span x-text="userAccumulation.masuk + 'x'"></span>
                                            </div>
                                            <div class="col-span-1 flex flex-row justify-between">
                                                <strong>Terlambat</strong>
                                                <span x-text="userAccumulation.terlambat + 'x'"></span>
                                            </div>
                                            <div class="col-span-1 flex flex-row justify-between">
                                                <strong>Sakit</strong>
                                                <span x-text="userAccumulation.ijin + 'x'"></span>
                                            </div>
                                            <div class="col-span-1 flex flex-row justify-between">
                                                <strong>Ijin</strong>
                                                <span x-text="userAccumulation.sakit + 'x'"></span>
                                            </div>
                                            <div class="col-span-1 flex flex-row justify-between">
                                                <strong>Tidak Masuk</strong>
                                                <span
                                                    class="text-red-500" 
                                                    x-text="userAccumulation.tidak_masuk + 'x'"
                                                ></span>
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
                                    <th 
                                        class="text-glassmorphism text-white"
                                        x-text="todayPresences.length > 0 ? 'Presensi hari ini' : 'Tidak ada yang presensi hari ini.'"
                                    ></th>
                                </tr>
                                <template x-if="todayPresences.length > 0">
                                    <tr>
                                        <th class="text-glassmorphism text-white">No</th>
                                        <th class="text-glassmorphism text-white">Nama</th>
                                        <th class="text-glassmorphism text-white">Jam presensi</th>
                                    </tr>
                                </template>
                            </thead>
                            <tbody>
                                <template x-for="(presence, index) in todayPresences" :key="presence.nama_karyawan">
                                    <tr>
                                        <td x-text="index + 1"></td>
                                        <td x-text="presence.nama_karyawan"></td>
                                        <td x-text="formatTime(presence.tanggal)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

</x-layouts.presensi>