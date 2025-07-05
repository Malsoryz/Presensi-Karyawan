<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'app_configs';

    protected $primaryKey = 'key';
    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
        'category',
    ];
}
