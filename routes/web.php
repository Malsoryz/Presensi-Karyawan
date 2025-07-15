<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresensiControllers;
use App\Http\Controllers\HariLiburControllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

// use App\Enums\StatusPresensi as SP;

Route::get('/', function () {
    return redirect()->route('presensi.index');
})->name('root');

Route::get('/presensi', [PresensiControllers::class, 'index'])
    ->name('presensi.index')
    ->middleware('auth');
Route::get('/presensi/store/{name}/{token}', [PresensiControllers::class, 'store'])
    ->name('presensi.store')
    ->middleware('auth');
Route::get('/presensi/scan-check', [PresensiControllers::class, 'scanCheck'])
    ->name('presensi.scanCheck')
    ->middleware('auth');

Route::post('logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');

Route::get('login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

Route::get('test', [PresensiControllers::class, 'test']);

Route::get('/random-color', function () {
    function random_bg() {
        return strtoupper(dechex(rand(0, 10000000)));
    }

    $color = random_bg();
    return view('random-color', compact('color'));
});

Route::get('/scan-qr', function () {
    $imageFiles = File::files(public_path('images/backgrounds'));
    $imagePaths = array_map(function ($file) {
        return 'images/backgrounds/' . $file->getFilename();
    }, $imageFiles);

    // Dapatkan index berdasarkan tanggal (agar tetap sama selama 1 hari)
    $dayIndex = date('z') % count($imagePaths); // 'z' = hari ke-berapa dalam tahun (0-365)
    $backgroundImage = $imagePaths[$dayIndex];

    return view('scan-qr', compact('backgroundImage'));
});


