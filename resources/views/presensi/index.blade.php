<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Presensi QR Code</title>
    @vite('resources/css/app.css') {{-- pastikan tailwind terpasang --}}
</head>
<body class="bg-base-200 min-h-screen flex items-center justify-center">

    <div class="card shadow-xl bg-white p-6 w-full max-w-sm">
        <h2 class="text-xl font-bold text-center mb-4">Scan QR Code untuk Presensi</h2>

        <div class="flex justify-center mb-4">
            {!! $qrCode !!}
        </div>

        <div class="text-sm text-gray-600 text-center">
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
            <p><strong>Jam:</strong> <span id="clock">--:--:--</span></p>
            <p><strong>Status:</strong> Menunggu scan...</p>
        </div>
    </div>

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

</body>
</html>
