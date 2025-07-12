<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Config;
use Carbon\Carbon;

use App\Console\Commands\UpdateHoliday;
use App\Console\Commands\CheckPresence;

function getExpiredTime(string $time)
{
    $toleransi = (int) Config::get('toleransi_presensi', 0);
    return Carbon::createFromFormat('Y-m-d H:i:s', $time)
        ->addMinutes($toleransi)
        ->addMinute()
        ->format('H:i');
}

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(UpdateHoliday::class)
    ->timezone(Config::get('timezone', 'Asia/Makassar'))
    ->yearly();

Schedule::command(CheckPresence::class)
    ->timezone(Config::get('timezone', 'Asia/Makassar'))
    ->dailyAt(getExpiredTime(Config::get('presensi_pagi_selesai', '09:00:00')));

Schedule::command(CheckPresence::class)
    ->timezone(Config::get('timezone', 'Asia/Makassar'))
    ->dailyAt(getExpiredTime(Config::get('presensi_siang_selesai', '15:00:00')));
