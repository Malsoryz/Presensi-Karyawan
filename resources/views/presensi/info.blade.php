@extends('Layout.Presensi')

@section('title', 'Presensi QR Code')

@section('content')
<div class="flex gap-16">
    <span class="text-4xl font-bold">
        @if (isset($status))
            {{ $status }}
        @endif

        @if (isset($presenceTime))
            {{ $presenceTime }}
        @endif

        @if (isset($presenceSession))
            {{ $presenceSession }}
        @endif
    </span><br>
    
    @if (isset($debug))
        <span>{{ var_dump($debug) }}</span>
    @endif
</div>
@endsection