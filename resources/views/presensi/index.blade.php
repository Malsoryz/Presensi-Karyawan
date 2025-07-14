@extends('Layout.Presensi')

@section('title', 'Presensi QR Code')

@section('content')
<div class="flex gap-16">
    <div class="flex gap-16 flex-col">
        <div class="p-2 rounded-md bg-white">
            {!! QrCode::size(256)->generate(route('presensi.store', ['name' => $name, 'token' => $token])) !!}
        </div>
        <div>
            <span>
                “ Nothing worth having comes easy. ” <br>
                — Theodore Roosevelt <br>
                {{ $status }} <br>
                {{ $presenceSession }}
            </span>
        </div>
    </div>
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
    <table class="table">
        <!-- head -->
        <thead>
            <tr>
                <th></th>
                <th>Nama</th>
                <th>Masuk</th>
                <th>Terlambat</th>
                <th>Ijin</th>
                <th>Sakit</th>
                <th>Tidak masuk</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($topThreePresence as $presensi)
                <tr>
                    <th>{{ $loop->iteration }}</th>
                    <td>{{ $presensi->nama_karyawan }}</td>
                    <td>{{ $presensi->total_masuk }}</td>
                    <td>{{ $presensi->total_terlambat }}</td>
                    <td>{{ $presensi->total_ijin }}</td>
                    <td>{{ $presensi->total_sakit }}</td>
                    <td>{{ $presensi->total_tidak_masuk }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endsection

@section('scripts')
<script>

let isAlreadyRedirected = false;

function presenceCheck() {
    axios.get("{{  route('presensi.scanCheck') }}")
        .then(response => {
            const isPresence = response.data.is_presence;
            const presenceSession = "{{ $presenceSession }}";

            if (isPresence && presenceSession !== 'sesi presensi') {
                isAlreadyRedirected = true;
                location.reload();
            }
        })
}

setInterval(presenceCheck, 3000);

setTimeout(() => {
    if (!isAlreadyRedirecting) {
        location.reload();
    }
}, 60000);

</script>
@endsection
