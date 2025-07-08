@extends('Layout.Presensi')

@section('title', 'Presensi QR Code')

@section('content')
<div class="flex gap-16">
    <span class="text-4xl font-bold">{{ $status }}</span><br>
</div>
@endsection

@section('scripts')
<script>

let isAlreadyRedirected = false;

function presenceCheck() {
    axios.get("{{  route('presensi.scanCheck') }}")
        .then(response => {
            const isPresence = response.data.is_presence;
            const isTimeValid = response.data.is_time_valid;

            if (!isPresence && isTimeValid) {
                isAlreadyRedirected = true;
                location.reload();
            }
        })
}

setInterval(presenceCheck, 3000);

</script>
@endsection