<?php

namespace App\Models;

use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Tipe extends Model
{
    protected $table = 'tipe';

    protected $fillable = [
        'nama_tipe',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
