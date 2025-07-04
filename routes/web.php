<?php

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Http\Controllers\PresensiControllers;

Route::get('/', function () {
    return view('welcome');
})->name('root');

Route::get('/presensi', [PresensiControllers::class, 'index'])->name('presensi.qr');
Route::get('/presensi/store/{name}/{token}', [PresensiControllers::class, 'store'])->name('presensi.qr.store');
