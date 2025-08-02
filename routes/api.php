<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

Route::prefix('api')->controller(ApiController::class)->group(function () {
    Route::get('qrcode', 'presenceQrCode')->name('api.qrcode');
    Route::get('authuser', 'authUserData')->name('api.authuser');
    Route::get('presences', 'presencesData')->name('api.presences');
    Route::get('approval', 'checkApproval')->name('api.approval');
});