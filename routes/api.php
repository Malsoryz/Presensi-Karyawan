<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::prefix('api')->controller(ApiController::class)->group(function () {
    Route::get('qrcode', 'presenceQrCode')->name('api.qrcode');
    Route::get('authuser', 'authUserData')->name('api.authuser');
    Route::get('presences', 'presencesData')->name('api.presences');
    Route::get('approval', 'checkApproval')->name('api.approval');
    Route::get('motivation', 'motivation')->name('api.motivation');
    Route::get('datetime', 'getDatetime')->name('api.datetime');
    Route::get('presences/status', 'presencesStatus')->name('api.presences.status');
    Route::get('background', 'backgrounds')->name('api.background');
});