<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Config;
use Carbon\Carbon;

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

    public static function randomImage(): ?string
    {
        $now = now(Config::timezone());
        $isFriday = $now->isFriday();
        $background = self::where('special_friday', $isFriday)->inRandomOrder();
        return $background->exists() ? asset("storage/{$background->first()->image_path}") : null;
    }

    public static function countImage(): int
    {
        $now = now(Config::timezone());
        $isFriday = $now->isFriday();
        return self::where('special_friday', $isFriday)->count();
    }
}
