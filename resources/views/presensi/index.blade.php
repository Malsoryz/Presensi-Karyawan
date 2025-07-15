<!DOCTYPE html>
<html>
<head>
    <title>Presensi QR Code</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            background: #f0f4f8;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .qr-code {
            margin: 20px 0;
        }

        .info {
            text-align: left;
            margin-top: 20px;
        }

        .info p {
            margin: 6px 0;
        }

        @media (max-width: 500px) {
            .card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Scan QR Code untuk Presensi</h2>

            <div class="qr-code">

                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ url('/presensi/scan') }}" alt="QR Code">
            </div>

            <div class="info">
                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
                <p><strong>Jam:</strong> <span id="clock">--:--:--</span></p>
                <p><strong>Status:</strong> Menunggu scan...</p>
            </div>
        </div>
    </div>

</body>
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

</html>