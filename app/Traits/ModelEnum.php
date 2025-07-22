<?php

namespace App\Traits;

trait ModelEnum
{
    public static function toArray(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    public static function toSelectItem(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = $case->name;
            return $carry;
        }, []);
    }
}
