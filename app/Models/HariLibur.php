<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HariLibur extends Model
{
    protected $table = 'hari_libur';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'tanggal',
    ];

    public static function hariKerjaTahunan(int $year)
    {
        $startDate = Carbon::create($year, 1, 1)->format('Y-m-d');
        $endDate = Carbon::create($year, 12, 31)->format('Y-m-d');

        $dates = DB::raw("(
            WITH RECURSIVE dates AS (
                SELECT DATE('$startDate') AS date
                UNION ALL
                SELECT DATE_ADD(date, INTERVAL 1 DAY)
                FROM dates
                WHERE dates < DATE('$endDate')
            )
            SELECT date from dates
        ) AS dates");

        return DB::table($dates)
            ->leftJoin('hari_libur as hl', 'hl.tanggal', '=', 'dates.date')
            ->selectRaw('
                MONTH(dates.dt) AS bulan,
                COUNT(*) AS total_hari,
                SUM(CASE WHEN DAYOFWEEK(dates.dt) = 1 THEN 1 ELSE 0 END) AS total_hari_minggu,
                SUM(CASE WHEN hl.tanggal IS NOT NULL THEN 1 ELSE 0 END) AS total_hari_libur_nasional,
                SUM(CASE WHEN DAYOFWEEK(dates.dt) = 1 OR hl.tanggal IS NOT NULL THEN 1 ELSE 0 END) AS total_hari_libur,
                COUNT(*) - SUM(CASE WHEN DAYOFWEEK(dates.dt) = 1 OR hl.tanggal IS NOT NULL THEN 1 ELSE 0 END) AS total_hari_kerja
            ')
            ->groupByRaw('MONTH(dates.dt)')
            ->orderBy('bulan');
    }
}
