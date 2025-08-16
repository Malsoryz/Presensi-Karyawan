<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $table = 'quotes';

    public $timestamps = false;
    
    protected $fillable = [
        'motivation',
        'author',
    ];
}
