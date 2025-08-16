<?php

namespace App\Models;

use App\Enums\Presensi\StatusPresensi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Stringable;

class PesanStatus extends Model
{
    protected $table = 'pesan_status';

    public $timestamps = false;

    protected $fillable = [
        'template',
        'type',
    ];

    public static function fillStatus(Stringable|string $target, StatusPresensi $status): Stringable
    {
        $result = $target instanceof Stringable ? $target : str($target);
        return $result->replace(':{status}', $status->display());
    }

    public static function queryFrom(StatusPresensi|string $status)
    {
        $result = $status instanceof StatusPresensi ? $status : StatusPresensi::tryFrom($status);
        return self::query()->where('type', $result->value);
    }

    public static function queryMasuk()
    {
        return self::query()->where('type', StatusPresensi::Masuk->value);
    }

    public static function queryTerlambat()
    {
        return self::query()->where('type', StatusPresensi::Terlambat->value);
    }
}
