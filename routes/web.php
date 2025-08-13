<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PresensiControllers;
use App\Http\Controllers\AuthController;

Route::redirect('/', '/presensi');

Route::prefix('presensi')->controller(PresensiControllers::class)->group(function () {
    Route::get('/', 'index')
        ->name('presensi.index');
    Route::get('/store/{token}', 'store')
        ->name('presensi.store')
        ->middleware('auth');

    Route::get('/test', 'test')
        ->name('presensi.test');
});

require __DIR__.'/auth.php';
require __DIR__.'/api.php';
