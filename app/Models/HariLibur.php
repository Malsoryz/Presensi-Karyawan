<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Config as Cfg;

class HariLibur extends Model
{
    protected $table = 'hari_libur';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'tanggal',
        'bulan',
    ];

    // public static function getInMonth(string|int $month)
    // {
    //     return self::select('tanggal')->whereMonth('tanggal', $month)->get();
    // }

    public static function isHoliday()
    {
        $today = now(Cfg::timezone())->toDateString();
        return self::whereDate('tanggal', $today)->exists();
    }

    public static function todayHoliday()
    {
        $today = now(Cfg::timezone())->toDateString();
        return self::whereDate('tanggal', $today)->get();
    }

    public static function isSunday()
    {
        return now(Cfg::timezone())->isSunday();
    }
}
