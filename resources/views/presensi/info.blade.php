@extends('components.layouts.presensi')

@section('title', 'Presensi QR Code')

@section('content')

<div class="card p-4 glassmorphism flex flex-col items-center max-w-120">
    <div>
        @if (isset($status))
            {!! $status->icon('drop-shadow-sm/20') !!}
        @else
            {!! \App\Enums\Icons::Info->render() !!}
        @endif
    </div><br>
    <span class="block text-white text-center text-3xl glassmorphism-text">
        @if (isset($presenceSession))
            @php
                $isSelesai = $presenceSession->value === 'selesai';
                $isLibur = ($presenceSession->value == 'libur' || $presenceSession->value == 'belum mulai');
            @endphp
            @if ($isSelesai)
                {{ $status->message() }}
            @elseif ($isLibur)
                {!! $presenceSession->message() !!}
            @endif
        @elseif (isset($message))
            {{ $message }}
        @endif        
    </span>
    {{-- Waktu Presensi --}}
    @if (isset($presenceTime) && in_array($status, ['masuk', 'terlambat']))
        <br><span class="block text-white text-center glassmorphism-text">pada {{ $presenceTime }}</span>
    @endif

    {{-- Timer --}}
    @if (isset($presenceSession))
        @if ($presenceSession->value == 'belum mulai' && isset($presenceStartTime, $now))
            <br><span id="presensiMulai">--:--:--</span>
        @endif
    @endif
</div>
@endsection

@section('scripts')
@if (isset($presenceSession))
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
@endif
@endsection