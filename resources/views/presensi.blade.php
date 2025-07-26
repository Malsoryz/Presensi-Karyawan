<x-layout title="Presensi" x-data x-init="updateUser">
    <x-slot name="header">
        {{-- Untuk menampilkan nama user yang login --}}
        <div class="navbar px-8 py-4 w-full fixed top-0 left-0 right-0">
            <div class="navbar-start">
                <button class="btn btn-soft">Login</button>
            </div>
            <div class="navbar-end">
                <div>
                    <span 
                        class="btn btn-soft"
                        x-text="$store.presensi.user?.name"
                    ></span>
                </div>
            </div>
        </div>
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
                            x-data="svgRefresh()" 
                            x-init="start()" 
                            x-html="svg"
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
            intervalId: null,
        }));
    });

    function updateUser() {
        const store = Alpine.store('presensi');

        // Cegah polling ganda
        if (store.intervalId) return;

        getData();
        store.intervalId = setInterval(() => {
            console.log('memuat ulang');
            getData();
        }, 3000);
    }

    function getData() {
        const store = Alpine.store('presensi');

        // Cegah polling ganda
        axios.get("{{ route('presensi.get-user') }}")
            .then(res => {
                if (res.data.user) {
                    Object.assign(store.user, res.data.user);
                }
                store.message = res.data.message;
                store.isDetected = res.data.is_detected;
                console.log('user ' + store.user.name);
                console.log('is detected ' + store.is_detected);

                // Hentikan polling jika terdeteksi
                if (store.isDetected) {
                    console.log('user ' + store.user.name);
                    clearInterval(store.intervalId);
                    store.intervalId = null;
                    console.log('Polling dihentikan karena user terdeteksi');
                }
            });
    }

    function svgRefresh() {
        return {
            svg: '',
            start() {
                console.log('Memulai membuat qr code baru');
                this.loadSvg();
                setInterval(() => {
                    console.log('membuat qr code baru');
                    this.loadSvg();
                }, 60000);
            },
            loadSvg() {
                axios.get("{{ route('presensi.getqr') }}")
                    .then(res => {
                        this.svg = res.data;
                    })
                    .catch(err => {
                        console.error('Gagal memuat svg: ', err);
                    });
            }
        }
    }
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