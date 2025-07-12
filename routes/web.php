<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresensiControllers;
use App\Http\Controllers\HariLiburControllers;
use Illuminate\Support\Facades\Auth;

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
Route::get('/presensi/info', [PresensiControllers::class, 'info'])
    ->name('presensi.info')
    ->middleware('auth');

Route::post('logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');

Route::get('login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');
