<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\PresensiControllers;

// Route::get('/home', function () {
//     return view('welcome');
// })->name('home');

Route::redirect('/', '/presensi');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Route::redirect('settings', 'settings/profile');

    // Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    // Volt::route('settings/password', 'settings.password')->name('settings.password');
    // Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::get('/presensi', [PresensiControllers::class, 'index'])
        ->name('presensi.index');
    Route::get('/presensi/store/{name}/{token}', [PresensiControllers::class, 'store'])
        ->name('presensi.store');
    Route::get('/presensi/scan-check', [PresensiControllers::class, 'scanCheck'])
        ->name('presensi.scanCheck');
});

require __DIR__.'/auth.php';
