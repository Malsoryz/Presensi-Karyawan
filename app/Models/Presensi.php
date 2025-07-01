<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $fillable = [
        'nama_karyawan',
        'jenis_presensi',
        'tanggal',
        'status_vaidasi_jaringan',
        'alamat_perangkat',
    ];
}
