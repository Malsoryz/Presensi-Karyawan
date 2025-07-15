@extends('layouts.app')

@section('title', 'Presensi QR Code')

@section('content')
<body style="background-image: url('{{ $bgPath }}'); background-size: cover; background-position: center;" class="min-h-screen text-base-content bg-fixed backdrop-blur-sm">

    <div class="min-h-screen flex items-center justify-center px-4 py-10">
        {{-- CARD UTAMA --}}
        <div class="card w-full max-w-4xl bg-base-100 bg-opacity-90 shadow-xl border border-base-300 p-8 rounded-2xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                {{-- BAGIAN KIRI: QR Code --}}
                <div class="flex flex-col items-center justify-center">
                    <h2 class="text-lg font-semibold text-center mb-3">Scan QR Code untuk Presensi</h2>

                    <div class="bg-white p-4 rounded-xl shadow">
                        {!! $qrCode !!}
                    </div>

                    <div class="flex flex-col items-center justify-center mt-3 text-sm text-gray-500 text-center">
                        <span class="italic leading-relaxed max-w-xs">
                            Setiap pagi adalah awal yang baru. Presensi bukan hanya hadir secara fisik, tapi juga hadir dengan semangat, niat, dan dedikasi untuk memberikan yang terbaik hari ini.
                        </span>
                    </div>


                </div>

                {{-- BAGIAN KANAN: Tabel Presensi --}}
                <div class="overflow-x-auto">
                    <h3 class="text-lg font-semibold text-center mb-12">Top 3 Presensi Hari Ini</h3>

                    <table class="table table-zebra w-full text-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Jam</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Farhan</td>
                                <td>07:30</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Ikmal</td>
                                <td>07:32</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Aspian</td>
                                <td>07:35</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- JAM --}}
    <script>
        function updateClock() {
            const now = new Date();
            const jam = String(now.getHours()).padStart(2, '0');
            const menit = String(now.getMinutes()).padStart(2, '0');
            const detik = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clock').textContent = `${jam}:${menit}:${detik}`;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

    {{-- ANIMASI LOADING --}}
    <style>
        .loading-dots::after {
            content: '.';
            animation: dots 1.5s steps(3, end) infinite;
        }

        @keyframes dots {
            0% { content: '.'; }
            33% { content: '..'; }
            66% { content: '...'; }
            100% { content: '.'; }
        }
    </style>
</body>
@endsection
