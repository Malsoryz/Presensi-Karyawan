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
                status {{ $status }}
            </span>
        </div>
    </div>
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-100">
    <table class="table">
        <!-- head -->
        <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Job</th>
                <th>Favorite Color</th>
                <th>Bla</th>
                <th>Bla</th>
                <th>Bla</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < 5; $i++)
                <tr>
                    <th>{{ $i }}</th>
                    <td>Dummy</td>
                    <td>Anything</td>
                    <td>Anything</td>
                    <td>Anything</td>
                    <td>Anything</td>
                    <td>Anything</td>
                </tr>
            @endfor
        </tbody>
    </table>
    </div>
</div>
@endsection

@section('scripts')
<script>

let isAlreadyRedirected = false;
const currentStatus = "{{ $status }}";

function presenceCheck() {
    axios.get("{{  route('presensi.scanCheck') }}")
        .then(response => {
            const isPresence = response.data.is_presence;
            const isTimeValid = response.data.is_time_valid;

            if (isPresence && currentStatus != 'presence') {
                // jika telah presensi
                isAlreadyRedirected = true;
                window.location.href = "{{ route('presensi.index', ['status' => 'presence']) }}";
            } else if ((!isPresence && !isTimeValid) && currentStatus != 'late') {
                // jika telat presensi
                isAlreadyRedirected = true;
                window.location.href = "{{ route('presensi.index', ['status' => 'late']) }}";
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
