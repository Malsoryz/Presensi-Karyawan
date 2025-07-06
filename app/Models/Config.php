<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    protected $table = 'app_configs';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'value',
    ];

    public static function getConfig(string $name): ?string
    {
        return self::where('name', $name)->value('value');
    }

    public static function setConfig(string $name, string $value): bool
    {
        return self::updateOrCreate(
            ['name' => $name],
            ['value' => $value]
        ) ? true : false;
    }
}
