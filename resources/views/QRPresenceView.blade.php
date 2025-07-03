<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex justify-center items-center min-h-screen w-full">
    <div class="flex gap-16">
        <div class="flex gap-16 flex-col">
            <div class="p-2 rounded-md bg-white">
                {!! QrCode::size(256)->generate($token) !!}
            </div>
            <div>
                <span>
                    “ Nothing worth having comes easy. ” <br>
                    — Theodore Roosevelt <br>
                    {{ $token }}
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
</body>
</html>
