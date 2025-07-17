@extends('components.layouts.presensi')

@section('title', 'Presensi QR Code')

@section('content')
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

    {{-- table --}}
    <div class="card p-4 overflow-x-auto glassmorphism">
        <table class="table table-lg">
            <thead>
                <tr>
                    <th class="text-white glassmorphism-text">No.</th>
                    <th class="text-white glassmorphism-text">Nama</th>
                    <th class="text-white glassmorphism-text">Masuk</th>
                    <th class="text-white glassmorphism-text">Terlambat</th>
                    <th class="text-white glassmorphism-text">Ijin</th>
                    <th class="text-white glassmorphism-text">Sakit</th>
                    <th class="text-white glassmorphism-text">Tidak masuk</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($topThreePresence as $presensi)
                    <tr>
                        <th class="text-white glassmorphism-text">{{ $loop->iteration }}</th>
                        <td class="text-white glassmorphism-text">{{ $presensi->nama_karyawan }}</td>
                        <td class="text-center text-green-400 glassmorphism-text">{{ $presensi->total_masuk }}</td>
                        <td class="text-center text-white glassmorphism-text">{{ $presensi->total_terlambat }}</td>
                        <td class="text-center text-white glassmorphism-text">{{ $presensi->total_ijin }}</td>
                        <td class="text-center text-white glassmorphism-text">{{ $presensi->total_sakit }}</td>
                        <td class="text-center text-red-400 glassmorphism-text">{{ $presensi->total_tidak_masuk }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
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
