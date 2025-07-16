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
        @yield('scripts')
    </head>
    <body 
        style="background-image: url('{{ asset('images/unsplash.jpg') }}');" 
        class="min-h-screen w-full bg-fixed bg-center bg-cover"
        >
        <header class="py-4 px-8 absolute">
            <form method="POST" action="{{ route('logout') }}" class="flex flex-row gap-2">
                @csrf
                <button type="submit" class="btn btn-soft">Log out <x-tabler-logout /></button>
                <div>
                    <span class="btn btn-soft">{{ auth()->user()->name }}</span>
                </div>
            </form>
        </header>
        <div class="flex justify-center items-center min-h-screen">
            @yield('content')
        </div>
    </body>
</html>
