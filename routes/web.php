<?php

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Http\Controllers\PresensiControllers;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/presensi-qr', [PresensiControllers::class, 'index']);
