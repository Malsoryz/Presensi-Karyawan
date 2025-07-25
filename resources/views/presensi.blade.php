<x-layout title="Presensi">
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
                            x-data="svgRefresh()" 
                            x-init="start()" 
                            x-html="svg"
                        ></div>
                    </div>
                    <div 
                        class="card glassmorphism w-72 h-32"
                        x-data="userData()"
                        x-init="start()"
                        x-text="message"
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
    function userData() {
        return {
            message: '',
            isDetected: false,
            intervalId: null,
            start() {
                this.getData();
                if (!this.intervalId && !this.isDetected) {
                    this.intervalId = setInterval(() => {
                        console.log('mendapatkan ulang data');
                        this.getData();

                        if (this.isDetected) {
                            clearInterval(this.intervalId);
                            this.intervalId = null;
                        }
                    }, 3000);
                }
            },
            getData() {
                axios.get("{{ route('presensi.get-user') }}")
                .then(res => {
                        this.message = res.data.message;
                        this.isDetected = res.data.is_detected;
                        console.log(this.message);
                        console.log('user terdeteksi: ' + this.isDetected);
                    });
            }
        }
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