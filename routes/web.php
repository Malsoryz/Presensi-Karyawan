<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresensiControllers;
use App\Http\Controllers\HariLiburControllers;

Route::get('/', function () {
    return redirect()->route('presensi.index');
});

Route::get('/presensi', [PresensiControllers::class, 'index'])
    ->name('presensi.index')
    ->middleware('auth');
Route::get('/presensi/store/{name}/{token}', [PresensiControllers::class, 'store'])
    ->name('presensi.store')
    ->middleware('auth');
Route::get('/presensi/scan-check', [PresensiControllers::class, 'scanCheck'])
    ->name('presensi.scanCheck')
    ->middleware('auth');
Route::get('/presensi/info', [PresensiControllers::class, 'info'])
    ->name('presensi.info')
    ->middleware('auth');

Route::get('/harilibur', function () {
    $url = 'https://api-harilibur.vercel.app/api';

    $response = Http::get($url, [
        // 'month' => 3,
        'year' => now()->year,
    ])->json();

    $listHariLibur = array_filter($response, function($res) {
        return $res['is_national_holiday'] === true;
    });

    return $listHariLibur;
});

Route::get('login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');
