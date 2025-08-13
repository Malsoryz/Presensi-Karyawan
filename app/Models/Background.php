<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Config;
use Carbon\Carbon;

use Illuminate\Support\Collection;

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

    // buat function untuk mengambil atau membuat query mengenai untuk jumat atau tidak

    public static function whereFriday()
    {
        $now = now(Config::timezone());
        return self::where('special_friday', $now->isFriday());
    }
}
