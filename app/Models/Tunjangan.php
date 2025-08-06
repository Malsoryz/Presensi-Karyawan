<?php

namespace App\Models;

use App\Models\Jabatan;

use Illuminate\Database\Eloquent\Model;

class Tunjangan extends Model
{
    protected $table = 'tunjangan';

    protected $fillable = [
        'nama',
        'jumlah',
        'jabatan_id',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
        ];
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
}
