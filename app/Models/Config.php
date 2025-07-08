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

    public static function get(string $name, $default = null): ?string
    {
        $result = self::where('name', $name)->value('value');

        if ($result == null) {
            return $default;
        }

        return $result;
    }

    public static function set(string $name, $value): bool
    {
        return self::updateOrCreate(
            ['name' => $name],
            ['value' => $value]
        ) ? true : false;
    }
}
