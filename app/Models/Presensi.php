<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class Presensi extends Model
{
    protected $table = 'presensi';

    public $timestamps = false;

    protected $fillable = [
        'nama_karyawan',
        'jenis_presensi',
        'tanggal',
        'status',
        'ip_address',
    ];

    public static function getCalculatedAll()
    {
        return self::query()
            ->select(
                'nama_karyawan',
                DB::raw('SUM(CASE WHEN status = "masuk" THEN 1 ELSE 0 END) as total_masuk'),
                DB::raw('SUM(CASE WHEN status = "terlambat" THEN 1 ELSE 0 END) as total_terlambat'),
                DB::raw('SUM(CASE WHEN status = "ijin" THEN 1 ELSE 0 END) as total_ijin'),
                DB::raw('SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as total_sakit'),
                DB::raw('SUM(CASE WHEN status = "tidak_masuk" THEN 1 ELSE 0 END) as total_tidak_masuk'),
            )->groupBy('nama_karyawan')->orderBy('nama_karyawan');
    }
}
