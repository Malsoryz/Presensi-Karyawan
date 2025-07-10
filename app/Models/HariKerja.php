<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HariKerja extends Model
{
    protected $table = 'hari_kerja';

    public $timestamps = false;

    protected $fillable = [
        'bulan',
        'total_hari',
        'total_hari_minggu',
        'total_hari_libur_nasional',
        'total_hari_libur',
        'total_hari_kerja',
    ];
}
