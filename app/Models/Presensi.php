<?php

namespace App\Models;

use App\Models\User;
use App\Models\Config;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

use App\Enums\Presensi\StatusPresensi;
use App\Enums\Presensi\JenisPresensi;

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
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getTotalQuery()
    {
        $status = collect(StatusPresensi::toArray());

        $rawQueries = $status->map(function ($item) {
            return DB::raw("SUM(CASE WHEN status = '{$item}' THEN 1 ELSE 0 END) as total_{$item}");
        })->toArray();

        return self::query()
            ->join('users', 'users.id', '=', 'presensi.user_id')
            ->select(
                'users.id',
                'users.name as nama_karyawan',
                ...$rawQueries,
            )
            ->groupBy('users.id', 'users.name')
            ->orderByRaw("SUM(CASE WHEN status = 'masuk' THEN 1 ELSE 0 END) DESC");
    }

    public static function getThisMonth()
    {
        $now = now(Config::timezone());
        $status = collect(StatusPresensi::toArray());

        $rawQueries = $status->map(function ($item) use ($now) {
            return DB::raw("SUM(CASE WHEN status = '{$item}' AND MONTH(tanggal) = {$now->month} THEN 1 ELSE 0 END) as total_{$item}");
        })->toArray();

        return self::query()
            ->join('users', 'users.id', '=', 'presensi.user_id')
            ->select(
                'users.id',
                'users.name as nama_karyawan',
                ...$rawQueries,
            )
            ->groupBy('users.id', 'users.name')
            ->orderByRaw("SUM(CASE WHEN status = 'masuk' AND MONTH(tanggal) = {$now->month} THEN 1 ELSE 0 END) DESC");
    }

    public static function getThisYear()
    {
        $now = now(Config::timezone());
        $status = collect(StatusPresensi::toArray());

        $rawQueries = $status->map(function ($item) use ($now) {
            return DB::raw("SUM(CASE WHEN status = '{$item}' AND YEAR(tanggal) = {$now->year} THEN 1 ELSE 0 END) as total_{$item}");
        })->toArray();

        return self::query()
            ->join('users', 'users.id', '=', 'presensi.user_id')
            ->select(
                'users.id',
                'users.name as nama_karyawan',
                ...$rawQueries,
            )
            ->groupBy('users.id', 'users.name')
            ->orderByRaw("SUM(CASE WHEN status = 'masuk' AND YEAR(tanggal) = {$now->year} THEN 1 ELSE 0 END) DESC");
    }

    public static function isTodayPresence(string $name): bool
    {
        $today = now(Config::timezone())->toDateString();
        return self::where('nama_karyawan', $name)->whereDate('tanggal', $today)->exists();
    }

    public static function today()
    {
        $today = now(Config::timezone())->toDateString();
        return self::select('nama_karyawan', 'jenis_presensi', 'tanggal', 'status')
            ->whereDate('tanggal', $today);
    }

    public static function accumulatedUser(?string $name)
    {
        return (bool) $name ? self::select(
            'nama_karyawan',
                DB::raw('SUM(CASE WHEN status = "masuk" THEN 1 ELSE 0 END) as total_masuk'),
                DB::raw('SUM(CASE WHEN status = "terlambat" THEN 1 ELSE 0 END) as total_terlambat'),
                DB::raw('SUM(CASE WHEN status = "ijin" THEN 1 ELSE 0 END) as total_ijin'),
                DB::raw('SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as total_sakit'),
                DB::raw('SUM(CASE WHEN status = "tidak_masuk" THEN 1 ELSE 0 END) as total_tidak_masuk'),
            )->groupBy('nama_karyawan')->orderByDesc('total_masuk')->where('nama_karyawan', $name)->first() : null;
    }
}
