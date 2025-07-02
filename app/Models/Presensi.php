<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'presensi';

    protected $fillable = [
        'nama_karyawan',
        'jenis_presensi',
        'tanggal',
        'ip_address',
    ];
}
