@extends('components.layouts.presensi')

@section('title', 'Presensi QR Code')

@section('content')


@if (isset($presenceSession))
    @if ($presenceSession == 'selesai' && isset($status, $presenceTime))
        <div class="card p-4 glassmorphism flex flex-col items-center max-w-120">
            <div>
                <x-heroicon-o-check-circle class="text-green-400 w-24 h-24"/>
            </div><br>
            <span class="block text-center text-3xl">
                {{ $status->message() }}
            </span><br>
            <span class="block text-center">{{ $presenceTime }}</span>
        </div>
    @endif
@endif
@endsection