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
                {!! route('presensi.store', ['name' => $name, 'token' => $token]) !!}
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
    // You can add any additional scripts here if needed
</script>
@endsection
