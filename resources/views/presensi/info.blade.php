@extends('components.layouts.presensi')

@section('title', 'Presensi QR Code')

@section('content')
@if (isset($presenceSession))
    {{-- SESI TELAH SELESAI ATAU TELAH PRESENSI --}}
    @if ($presenceSession->value == 'selesai' && isset($status))
        <div class="card p-4 glassmorphism flex flex-col items-center max-w-120">
            <div>
                {!! $status->icon('drop-shadow-sm/20') !!}
            </div><br>
            <span class="block text-white text-center text-3xl glassmorphism-text">
                {{ $status->message() }}
            </span>
            @if (isset($presenceTime) && in_array($status, ['masuk', 'terlambat']))
                <br><span class="block text-white text-center glassmorphism-text">pada {{ $presenceTime }}</span>
            @endif
        </div>
    @endif

    {{-- SESI LIBUR ATAU HARI MINGGU --}}
    @if (($presenceSession->value == 'libur' || $presenceSession->value == 'belum mulai') && isset($status))
        <div class="card p-4 glassmorphism flex flex-col items-center max-w-120">
            <div>
                {!! $status->icon('drop-shadow-sm/20') !!}
            </div><br>
            <span class="block text-white text-center text-3xl glassmorphism-text">
                {!! $presenceSession->message() !!}
            </span>
            @if ($presenceSession->value == 'belum mulai' && isset($presenceStartTime, $now))
                <br><span id="presensiMulai">--:--:--</span>
            @endif
        </div>
    @endif
@endif
@endsection

@section('scripts')
@if ($presenceSession->value == 'belum mulai' && isset($presenceStartTime, $now))
<script>
    const now = new Date("{{ $now->format('Y-m-d H:i:s') }}").getTime();
    const presenceStartTime = new Date("{{ $presenceStartTime->format('Y-m-d H:i:s') }}").getTime();

    let remaining = Math.floor((presenceStartTime - now) / 1000);

    const countdownElement = document.getElementById('presensiMulai');

    const timer = setInterval(function () {
        if (remaining <= 0) {
            clearInterval(timer);
            countdownElement.textContent = "Waktu Habis!";
            location.reload();
            return;
        }

        const hours = Math.floor(remaining / 3600);
        const minutes = Math.floor((remaining % 3600) / 60);
        const seconds = remaining % 60;

        countdownElement.textContent =
            String(hours).padStart(2, '0') + ":" +
            String(minutes).padStart(2, '0') + ":" +
            String(seconds).padStart(2, '0');

        remaining--;
    }, 1000);
</script>
@endif
@endsection