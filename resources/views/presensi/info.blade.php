@extends('components.layouts.presensi')

@section('title', 'Presensi QR Code')

@section('content')
<div>
    <div class="flex flex-col items-center gap-4 card p-4 bg-base-300/20 isolate rounded-xl shadow-xl backdrop-blur-lg border border-base-content/5">
        <x-heroicon-o-check-circle class="text-green-400"/>
        <x-heroicon-o-exclamation-circle class="text-red-400"/>
        <x-heroicon-o-exclamation-triangle class="text-yellow-400"/>

        <div>
            @if (isset($status))
                {{ $status }}
            @endif
        </div>

        <div>
            @if (isset($presenceTime))
                {{ $presenceTime }}
            @endif
        </div>

        <div>
            @if (isset($presenceSession))
                {{ $presenceSession }}
            @endif
        </div>

        <div>
            @if (isset($hariLibur))
                @foreach ($hariLibur as $libur)
              
            </div>  {{ $libur->nama }} <br>
                {{ $libur->tanggal }}
            @endforeach
        @endif

        <div>
            @if (isset($hari))
                {{ $hari }}
            @endif
        </div>
    </div>
</div>
@endsection