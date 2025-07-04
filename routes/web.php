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
Route::get('/presensi/scan-check', [PresensiControllers::class, 'scanCheck'])
    ->name('presensi.scanCheck')
    ->middleware('auth');

Route::get('/presensi/presence', function (){
    return view('Presensi.presence');
})->name('presensi.presence')->middleware('auth');
Route::get('/presensi/late', function (){
    return view('Presensi.late');
})->name('presensi.late')->middleware('auth');

Route::get('login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');
