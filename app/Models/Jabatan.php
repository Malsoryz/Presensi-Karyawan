<?php

namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';

    protected $fillable = [
        'nama',
        'gaji_pokok_bulanan',
        'tunjangan_kehadiran_harian',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
