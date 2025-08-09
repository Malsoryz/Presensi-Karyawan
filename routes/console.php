<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Config;

use App\Console\Commands\UpdateWorkday;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule::command(UpdateWorkday::class)
//     ->timezone(Config::get('timezone', 'Asia/Makassar'))
//     ->yearly();
