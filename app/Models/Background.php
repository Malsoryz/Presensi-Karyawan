<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Background extends Model
{
    protected $fillable = [
        'name',
        'image_path',
        'special_friday'
    ];

    protected $casts = [
        'special_friday' => 'boolean',
    ];
}
