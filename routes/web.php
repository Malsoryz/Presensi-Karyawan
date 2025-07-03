<?php

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Http\Controllers\PresensiControllers;

Route::get('/', function () {
    return redirect('/admin');
})->name('root');

Route::get('/presensi-qr', [PresensiControllers::class, 'index'])->name('presensi.qr');
Route::get('/presensi-qr/store/{token}', [PresensiControllers::class, 'store'])->name('presensi.qr.store');
