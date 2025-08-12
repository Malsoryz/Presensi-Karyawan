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

    public static function getThisMonth()
    {
        $now = now(Config::timezone());
        $status = StatusPresensi::toArray();

        $monthColumn = array_combine($status, array_map(fn($column) => "total_{$column}", $status));

        $monthQuery = array_map(function ($stat) use ($now, $monthColumn) {
            return DB::raw("SUM(CASE WHEN status = '{$stat}' AND MONTH(tanggal) = {$now->month} THEN 1 ELSE 0 END) as {$monthColumn[$stat]}");
        }, $status);

        return self::query()
            ->join('users', 'users.id', '=', 'presensi.user_id')
            ->select(
                'users.id',
                'users.name as nama_karyawan',
                // month
                ...$monthQuery,
            )
            ->groupBy('users.id', 'users.name')
            ->orderByRaw("SUM(CASE WHEN status = 'masuk' AND MONTH(tanggal) = {$now->month} THEN 1 ELSE 0 END) DESC");
    }

    public static function getThisYear()
    {
        $now = now(Config::timezone());
        $status = StatusPresensi::toArray();

        $yearColumn = array_combine($status, array_map(fn($column) => "total_{$column}", $status));

        $yearQuery = array_map(function ($stat) use ($now, $yearColumn) {
            return DB::raw("SUM(CASE WHEN status = '{$stat}' AND YEAR(tanggal) = {$now->year} THEN 1 ELSE 0 END) as {$yearColumn[$stat]}");
        }, $status);

        return self::query()
            ->join('users', 'users.id', '=', 'presensi.user_id')
            ->select(
                'users.id',
                'users.name as nama_karyawan',
                // year
                ...$yearQuery,
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
        return (boolean) $name ? self::select(
            'nama_karyawan',
                DB::raw('SUM(CASE WHEN status = "masuk" THEN 1 ELSE 0 END) as total_masuk'),
                DB::raw('SUM(CASE WHEN status = "terlambat" THEN 1 ELSE 0 END) as total_terlambat'),
                DB::raw('SUM(CASE WHEN status = "ijin" THEN 1 ELSE 0 END) as total_ijin'),
                DB::raw('SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as total_sakit'),
                DB::raw('SUM(CASE WHEN status = "tidak_masuk" THEN 1 ELSE 0 END) as total_tidak_masuk'),
            )->groupBy('nama_karyawan')->orderByDesc('total_masuk')->where('nama_karyawan', $name)->first() : null;
    }
}
