<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Config extends Model
{
    protected $table = 'app_configs';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'value',
    ];

    public static function timezone($default = null): ?string
    {
        return self::where('name', 'timezone')->value('value') ?? $default;
    }

    public static function get(string $name, $default = null): ?string
    {
        return self::where('name', $name)->value('value') ?? $default;
    }

    public static function getTime(string $name, $default = null): Carbon
    {
        $result = self::where('name', $name)->value('value');
        
        if ($result == null) {
            return $default;
        }
        
        return Carbon::createFromTimeString($result, self::timezone());
    }

    public static function presencesTime(): array
    {
        return [
            'pagiMulai' => self::getTime('presensi_pagi_mulai', '08:00:00'),
            'pagiSelesai' => self::getTime('presensi_pagi_selesai', '09:00:00'),
            'siangMulai' => self::getTime('presensi_siang_mulai', '14:00:00'),
            'siangSelesai' => self::getTime('presensi_siang_selesai', '15:00:00')->addHours(now(self::timezone())->isFriday() ? 1 : 0),
            'toleransi' => (int) self::get('toleransi_presensi', 0),
            'timezone' => self::timezone(),
            'mulaiKerja' => self::getTime('jam_mulai_kerja', '08:00:00'),
            'pulangKerja' => Carbon::createFromTimeString('17:00:00', self::timezone()),
        ];
    }

    public static function set(string $name, $value): bool
    {
        return self::updateOrCreate(
            ['name' => $name],
            ['value' => $value]
        ) ? true : false;
    }
}
