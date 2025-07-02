<?php

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return view('test');
})->name('test');
