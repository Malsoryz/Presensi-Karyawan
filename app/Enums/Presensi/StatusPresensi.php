<?php

namespace App\Enums\Presensi;

use App\Enums\Icons;
use App\Traits\ModelEnum;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;

enum StatusPresensi: string {

    use ModelEnum;

    case Masuk = 'masuk';
    case Terlambat = 'terlambat';
    case Ijin = 'ijin';
    case Sakit = 'sakit';
    case TidakMasuk = 'tidak_masuk';

    public function label(): ?string
    {
        return match ($this) {
            self::Masuk => "Ontime",
            self::Terlambat => "Terlambat",
            self::Ijin => "Ijin",
            self::Sakit => "Sakit",
            self::TidakMasuk => "Tidak masuk",
            default => null,
        };
    }

    public static function enumForStatusMessage()
    {
        return collect(self::toArray())
            ->filter(fn ($item) => in_array($item, ['masuk', 'terlambat']))->toArray();
    }

    public static function itemForStatusMessage()
    {
        return collect(self::toSelectItem())
            ->filter(fn ($item) => in_array($item, ['Masuk', 'Terlambat']))
            ->map(fn ($value, $key) => $key === 'masuk' ? 'Ontime' : $value)
            ->toArray();
    }

    public function display(): HtmlString|string|null|\stdClass
    {
        $data = match ($this) {
            self::Masuk => (object)[
                'label' => "Ontime",
                'class' => "text-green-400"
            ],
            self::Terlambat => (object)[
                'label' => "Terlambat",
                'class' => "text-yellow-400",
            ],
            self::TidakMasuk => (object)[
                'label' => "Tidak masuk",
                'class' => "text-red-400",
            ],
            default => null,
        };

        return (string) new HtmlString(Blade::render(<<<'BLADE'
            <span class="{{ $class }}">{{ $label }}</span>
        BLADE, [
            'label' => $data->label,
            'class' => $data->class,
        ]));
    }
}