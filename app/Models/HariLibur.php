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
        'bulan',
    ];

    public static function getInMonth(string|int $month)
    {
        return self::select('tanggal')->whereMonth('tanggal', $month)->get();
    }

}
