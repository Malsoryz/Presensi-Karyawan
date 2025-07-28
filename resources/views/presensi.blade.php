<x-layout title="Presensi" x-data="userData" x-init="updateUser()">
    <x-slot name="header">
        <x-presensi.header/>
    </x-slot>
    
    <main class="min-h-screen w-full flex items-center justify-center">

        <div class="flex flex-col gap-4">
            <div class="card glassmorphism p-0.5 flex flex-row gap-1">
                <template x-for="tab in $x.tab.tabs" :key="tab.id">
                    <button
                        x-init="$x.tab.activeTab = $x.tab.tabs[0].id"
                        :class="$x.tab.activeTab === tab.id ? 'bg-base-300/10' : 'bg-transparent'"
                        class="btn rounded-xl border-none card glassmorphism-text text-white"
                        x-text="tab.label"
                        x-on:click="$x.tab.activeTab = tab.id"
                    ></button>
                </template>
            </div>

            <template x-if="$x.tab.activeTab === 'tab-presensi'">
                <div class="flex flex-row gap-4 items-stretch">
                    <div class="flex flex-col gap-4">
                        <div class="card glassmorphism w-72 h-32">
                            {{-- Jam dinamis --}}
                            @if (session('info'))
                                {{ session('info') }}
                            @endif
                        </div>
                        <div class="card glassmorphism w-72 h-72">
                            <div 
                                x-data="refreshQrCode" 
                                x-init="start()" 
                                x-html="$x.qrCode"
                            ></div>
                        </div>
                        <div 
                            class="card glassmorphism w-72 h-32"
                            x-text="$x.message"
                        >
                            {{-- Status --}}
    
                        </div>
                    </div>
                    <div class="card glassmorphism w-128">
                        @if (isset($message))
                            {{ $message }}
                        @endif
                        @if (isset($isPresenceAllowed))
                            {{ $isPresenceAllowed ? 'di ijinkan' : 'tidak di ijinkan' }}
                        @endif
                        @if (isset($todayHoliday))
                            @if ((boolean) $todayHoliday)
                                @foreach ($todayHoliday as $holiday)
                                    {{ $holiday }},
                                @endforeach
                            @endif
                        @endif
                        @if (isset($presenceStartAt))
                            {{ $presenceStartAt }}
                        @endif
                    </div>
                </div>
            </template>

        </div>
    </main>

<x-slot name="scriptAfter">
<script>
    document.addEventListener('alpine:init', () => {
        const globalStoreName = 'app';

        Alpine.magic('x', () => Alpine.store(globalStoreName));

        Alpine.store(globalStoreName, Alpine.reactive({
            user: {},
            message: '',
            isDetected: false,
            isLogin: false,
            qrCode: '',
            todayPresences: [],
            userAccumulation: {},
            tab: {
                activeTab: '',
                tabs: [
                    { id: 'tab-presensi', label: 'Presensi' },
                    { id: 'tab-detail', label: 'Detail' },
                ],
            },
        }));
        
        Alpine.data('userData', () => ({
            intervalId: null,
            updateUser() {
                // Cegah polling ganda
                if (Alpine.store(globalStoreName).intervalId) return;
    
                this.getData();
                this.intervalId = setInterval(() => {
                    console.log('memuat ulang');
                    this.getData();
                }, 3000);
            },
            getData() {
                axios.get("{{ route('presensi.get-user') }}")
                    .then(res => {
                        const store = Alpine.store(globalStoreName);

                        if (res.data.user) {
                            Object.assign(store.user, res.data.user);
                        }
                        store.message = res.data.message;
                        store.isDetected = res.data.is_detected;
                        store.isLogin = res.data.is_login;
    
                        if (store.isDetected) {
                            console.log(`user ${store.user.name} terdeteksi`);
                            clearInterval(this.intervalId);
                            this.intervalId = null;
                            console.log('Polling dihentikan karena user terdeteksi');
                        }
                    })
                    .catch(err => {
                        console.error('Gagal melakukan request: ', err);
                    });
            }
        }));

        Alpine.data('presencesData', () => ({
            refresh() {
                console.log('refresh data presensi');
                this.getData();
                setInterval(() => {
                    console.log('refresh data presensi');
                    this.getData();
                }, 10000);
            },
            getData() {
                axios.get("{{ route('presensi.data') }}", {
                    params: { name: Alpine.store(globalStoreName).user?.name }
                })
                .then(res => {
                    const store = Alpine.store(globalStoreName);

                    if (res.data.user_accumulation) {
                        Object.assign(store.userAccumulation, res.data.user_accumulation);
                    }
                    store.todayPresences = res.data.today_presences;
                })
                .catch(err => {
                    console.error('Gagal melakukan request: ', err);
                })
            }
        }));

        Alpine.data('refreshQrCode', () => ({
            start() {
                console.log('Membuat qr code baru');
                this.loadQrCode();
                setInterval(() => {
                    console.log('membuat qr code baru');
                    this.loadQrCode();
                }, 60000);
            },
            loadQrCode() {
                axios.get("{{ route('presensi.getqr') }}")
                    .then(res => {
                        Alpine.store(globalStoreName).qrCode = res.data;
                    })
                    .catch(err => {
                        console.error('Gagal memuat svg: ', err);
                    });
            }
        }));
    });
</script>
</x-slot>

</x-layout>

{{-- <div class="flex flex-col gap-4">
    <div class="card glassmorphism w-128 h-64">
        Akumulasi
    </div>
    <div class="card glassmorphism w-128 h-64">
        daftar yang sudah presensi
    </div>
</div> --}}