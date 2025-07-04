<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresensiControllers;

Route::get('/', function () {
    return redirect()->route('presensi.index');
});

Route::get('/presensi', [PresensiControllers::class, 'index'])
    ->name('presensi.index')
    ->middleware('auth');
Route::get('/presensi/store/{name}/{token}', [PresensiControllers::class, 'store'])
    ->name('presensi.store')
    ->middleware('auth');
Route::get('/presensi/scanned', [PresensiControllers::class, 'scanned'])
    ->name('presensi.scanned')
    ->middleware('auth');
Route::get('/presensi/scan-check', [PresensiControllers::class, 'scanCheck'])
    ->name('presensi.scanCheck')
    ->middleware('auth');

Route::get('login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');
