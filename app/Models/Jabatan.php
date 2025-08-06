<?php

namespace App\Models;

use App\Models\User;
use App\Models\Tunjangan;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';

    protected $fillable = [
        'nama',
        'gaji_pokok_bulanan',
    ];

    public function casts(): array
    {
        return [
            'gaji_pokok_bulanan' => 'integer',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function tunjangan()
    {
        return $this->hasMany(Tunjangan::class, 'jabatan_id');
    }
}
