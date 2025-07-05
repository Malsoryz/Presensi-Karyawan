<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
            </style>
        @endif
        <script>

const currentRoute = "{{ Route::currentRouteName() }}";
let isAlreadyRedirecting = false;

function cekPresensi() {
    axios.get("{{ route('presensi.scanCheck') }}")
        .then(response => {
            const isPresence = response.data.is_presence;
            if (isPresence && currentRoute === 'presensi.index' && !isAlreadyRedirecting) {
                isAlreadyRedirecting = true;
                window.location.href = "{{ route('presensi.presence') }}";
            } else if (!isPresence && currentRoute === 'presensi.presence' && !isAlreadyRedirecting) {
                isAlreadyRedirecting = true;
                window.location.href = "{{ route('presensi.index') }}";
            }
        })
        .catch(error => {
            console.error('Error checking presensi status:', error);
        });
}

setInterval(cekPresensi, 3000);

setTimeout(() => {
    if (!isAlreadyRedirecting) {
        location.reload();
    }
}, 60000);

        </script>
    </head>
    <body class="flex justify-center items-center min-h-screen w-full">
        @yield('content')
        @yield('scripts')
    </body>
</html>
