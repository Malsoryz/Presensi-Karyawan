<x-layout title="Presensi" x-data x-init="$store.presensi.updateUser()">
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
                        @if (session('info'))
                            {{ session('info') }}
                        @endif
                    </div>
                    <div class="card glassmorphism w-72 h-72">
                        <div 
                            x-data="refreshQrCode" 
                            x-init="start()" 
                            x-html="$store.presensi.qrCode"
                        ></div>
                    </div>
                    <div 
                        class="card glassmorphism w-72 h-32"
                        x-text="$store.presensi.message"
                    >
                        {{-- Status --}}

                    </div>
                </div>
                <div class="card glassmorphism w-128">
                    {{-- Motivasi --}}
                </div>
            </div>
        </div>
    </main>

<x-slot name="scriptAfter">
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('presensi', Alpine.reactive({
            user: {},
            message: '',
            isDetected: false,
            isLogin: false,
            intervalId: null,
            qrCode: '',
            updateUser() {
                // Cegah polling ganda
                if (this.intervalId) return;

                this.getData();
                this.intervalId = setInterval(() => {
                    console.log('memuat ulang');
                    this.getData();
                }, 3000);
            },
            getData() {
                axios.get("{{ route('presensi.get-user') }}")
                    .then(res => {
                        if (res.data.user) {
                            Object.assign(this.user, res.data.user);
                        }
                        this.message = res.data.message;
                        this.isDetected = res.data.is_detected;
                        this.isLogin = res.data.is_login;

                        // Hentikan polling jika terdeteksi
                        if (this.isDetected) {
                            console.log(`user ${this.user.name} terdeteksi`);
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
                        Alpine.store('presensi').qrCode = res.data;
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