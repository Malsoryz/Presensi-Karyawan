@extends('components.layouts.presensi')

@section('title', 'Presensi QR Code')

@section('content')
<div class="flex flex-row gap-4">

    <div class="flex flex-col gap-4 items-center">

        <div class="card p-4 bg-base-300/20 isolate rounded-xl shadow-xl backdrop-blur-lg border border-base-content/5">
            <div class="p-2 rounded-md bg-white max-w-fit w-fit min-w-fit">
                {!! QrCode::size(256)->generate(route('presensi.store', ['name' => $name, 'token' => $token])) !!}
            </div>
        </div>

        <div class="card p-4 bg-base-300/20 isolate rounded-xl shadow-xl backdrop-blur-lg border border-base-content/5">
            <span class="text-white text-shadow-sm/20 mix-blend-difference break-words max-w-68">
                “ Nothing worth having comes easy. ” <br>
                — Theodore Roosevelt <br>
            </span>
        </div>
    </div>

    {{-- table --}}
    <div class="card p-4 overflow-x-auto border border-base-content/5 bg-base-300/20 isolate rounded-xl shadow-xl backdrop-blur-lg">
        <table class="table table-lg">
            <thead>
                <tr>
                    <th class="text-white text-shadow-sm/20 mix-blend-difference">No.</th>
                    <th class="text-white text-shadow-sm/20 mix-blend-difference">Nama</th>
                    <th class="text-white text-shadow-sm/20 mix-blend-difference">Masuk</th>
                    <th class="text-white text-shadow-sm/20 mix-blend-difference">Terlambat</th>
                    <th class="text-white text-shadow-sm/20 mix-blend-difference">Ijin</th>
                    <th class="text-white text-shadow-sm/20 mix-blend-difference">Sakit</th>
                    <th class="text-white text-shadow-sm/20 mix-blend-difference">Tidak masuk</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($topThreePresence as $presensi)
                    <tr>
                        <th class="text-white text-shadow-sm/20 mix-blend-difference">{{ $loop->iteration }}</th>
                        <td class="text-white text-shadow-sm/20 mix-blend-difference">{{ $presensi->nama_karyawan }}</td>
                        <td class="text-center text-green-400 text-shadow-sm/20 mix-blend-difference">{{ $presensi->total_masuk }}</td>
                        <td class="text-center text-white text-shadow-sm/20 mix-blend-difference">{{ $presensi->total_terlambat }}</td>
                        <td class="text-center text-white text-shadow-sm/20 mix-blend-difference">{{ $presensi->total_ijin }}</td>
                        <td class="text-center text-white text-shadow-sm/20 mix-blend-difference">{{ $presensi->total_sakit }}</td>
                        <td class="text-center text-red-400 text-shadow-sm/20 mix-blend-difference">{{ $presensi->total_tidak_masuk }}</td>
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
