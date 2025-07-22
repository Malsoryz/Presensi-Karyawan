<?php

namespace App\Models;

use App\Models\User;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getTotalQuery()
    {
        return self::query()
            ->select(
                'nama_karyawan',
                DB::raw('SUM(CASE WHEN status = "masuk" THEN 1 ELSE 0 END) as total_masuk'),
                DB::raw('SUM(CASE WHEN status = "terlambat" THEN 1 ELSE 0 END) as total_terlambat'),
                DB::raw('SUM(CASE WHEN status = "ijin" THEN 1 ELSE 0 END) as total_ijin'),
                DB::raw('SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as total_sakit'),
                DB::raw('SUM(CASE WHEN status = "tidak_masuk" THEN 1 ELSE 0 END) as total_tidak_masuk'),
            )->groupBy('nama_karyawan')->orderByDesc('total_masuk');
    }

    public static function getTotal()
    {
        return self::select(
            'nama_karyawan',
                DB::raw('SUM(CASE WHEN status = "masuk" THEN 1 ELSE 0 END) as total_masuk'),
                DB::raw('SUM(CASE WHEN status = "terlambat" THEN 1 ELSE 0 END) as total_terlambat'),
                DB::raw('SUM(CASE WHEN status = "ijin" THEN 1 ELSE 0 END) as total_ijin'),
                DB::raw('SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as total_sakit'),
                DB::raw('SUM(CASE WHEN status = "tidak_masuk" THEN 1 ELSE 0 END) as total_tidak_masuk'),
            )->groupBy('nama_karyawan')->orderByDesc('total_masuk');
    }
}
