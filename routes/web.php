<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\PresensiControllers;

Route::redirect('/', '/presensi');

Route::prefix('presensi')->controller(PresensiControllers::class)->group(function () {
    Route::get('/', 'index')
        ->name('presensi.index');
    Route::get('/store/{token}', 'store')
        ->name('presensi.store')
        ->middleware('auth');
    Route::get('/get-qr', 'getQr')
        ->name('presensi.getqr');
    Route::get('/get-user', 'getUser')
        ->name('presensi.get-user');
        
    // Route::get('/check-cookie', 'checkCookie')
    //     ->name('presensi.check-cookie');

    Route::get('/data', 'getPresenceData')
        ->name('presensi.data');

    Route::get('/test', 'test')
        ->name('presensi.test');
});

require __DIR__.'/auth.php';
