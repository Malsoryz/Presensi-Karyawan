@extends('components.layouts.presensi')

@section('title', 'Presensi QR Code')

@section('content')
@if (isset($presenceSession))

@if (Agent::isDesktop())
    <div class="flex flex-row gap-4">
        <div class="flex flex-col gap-4 items-center">
            <div class="card p-4 glassmorphism">
                @if (isset($token, $name))
                    <div class="p-2 rounded-md bg-white max-w-fit w-fit min-w-fit">
                        {!! QrCode::size(256)->generate(route('presensi.store', ['name' => $name, 'token' => $token])) !!}
                    </div>
                @endif
            </div>

            <div class="card p-4 glassmorphism">
                <span class="text-white glassmorphism-text break-words max-w-68">
                    “ Nothing worth having comes easy. ” <br>
                    — Theodore Roosevelt <br>
                </span>
            </div>
        </div>
        <div class="card p-4 overflow-x-auto glassmorphism">
            <table class="table table-lg">
                <thead>
                    <tr>
                        @foreach (['No.', 'Nama', 'Masuk', 'Terlambat', 'Ijin', 'Sakit', 'Tidak Masuk'] as $columnHeader)
                            <th @class([
                                'text-white',
                                'glassmorphism-text',
                                'rounded-md',
                                'hover:bg-base-100/20',
                            ])>
                                {{ $columnHeader }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topThreePresence as $presensi)
                        <tr @class([
                            'rounded-md',
                            'bg-base-100/10' => $name === $presensi->nama_karyawan,
                            'hover:bg-base-100/20',
                        ])>
                            <th @class([
                                'rounded-l-md',
                                'text-white' => !($name === $presensi->nama_karyawan),
                                'text-orange-300' => $name === $presensi->nama_karyawan,
                                'glassmorphism-text',
                            ])>
                                {{ $loop->iteration }}
                            </th>
                            <td @class([
                                'text-white' => !($name === $presensi->nama_karyawan),
                                'text-orange-300' => $name === $presensi->nama_karyawan,
                                'glassmorphism-text'
                            ])>
                                {{ $presensi->nama_karyawan }}
                            </td>
                            <td class="text-center text-green-400 glassmorphism-text">{{ $presensi->total_masuk }}</td>
                            <td class="text-center text-white glassmorphism-text">{{ $presensi->total_terlambat }}</td>
                            <td class="text-center text-white glassmorphism-text">{{ $presensi->total_ijin }}</td>
                            <td class="text-center text-white glassmorphism-text">{{ $presensi->total_sakit }}</td>
                            <td class="rounded-r-md text-center text-red-400 glassmorphism-text">{{ $presensi->total_tidak_masuk }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@if (Agent::isPhone())
    <div class="flex flex-col flex-2 justify-center items-center">
        <div class="card p-4 glassmorphism">
            <span class="text-white glassmorphism-text break-words max-w-68">
                “ Nothing worth having comes easy. ” <br>
                — Theodore Roosevelt <br>
            </span>
        </div>
    </div>

    <div class="flex flex-col flex-1 justify-center items-center">
        <a class="btn btn-secondary">
            Presensi
        </a>
    </div>
@endif

@endif
@endsection



@section('scripts')
<script>

function presenceCheck() {
    axios.get("{{ route('presensi.scanCheck') }}")
        .then(response => {
            const isPresence = response.data.is_presence;
            if (isPresence) {
                location.reload();
            }
        })
}

setInterval(presenceCheck, 3000);

setTimeout(() => location.reload(), 60000);

</script>
@endsection
