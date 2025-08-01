@props([
    'title' => null,
    'header' => null,
    'scriptAfter' => null,
    'scriptBefore' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title }}</title>

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
        {{ $scriptBefore }}
    </head>
    <body
        style="background-image: url('{{ asset('images/unsplash.jpg') }}');" 
        class="min-h-screen w-full bg-fixed bg-center bg-cover"
    >
        <div {{ $attributes }}>
            <header>
                {{ $header }}
            </header>
            {{ $slot }}
            {{ $scriptAfter }}
        </div>
    </body>
</html>
