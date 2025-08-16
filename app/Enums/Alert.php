<?php

namespace App\Enums;

use Illuminate\View\ComponentAttributeBag;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;

enum Alert {
    case Success;
    case Danger;
    case Error;
    case Info;
    case Neutral;

    public function getIcon(): Htmlable|null
    {
        return match ($this) {
            self::Success => new HtmlString(Blade::render(<<<'BLADE'
                <x-heroicon-o-check-circle class="text-green-400 h-8 w-8" />
            BLADE)),
            self::Danger => new HtmlString(Blade::render(<<<'BLADE'
                <x-heroicon-o-exclamation-triangle class="text-yellow-400 h-8 w-8" />
            BLADE)),
            self::Error => new HtmlString(Blade::render(<<<'BLADE'
                <x-heroicon-o-exclamation-circle class="text-red-400 h-8 w-8" />
            BLADE)),
            self::Neutral, self::Info => new HtmlString(Blade::render(<<<'BLADE'
                <x-heroicon-o-information-circle class="text-blue-400 h-8 w-8" />
            BLADE)),
            default => null,
        };
    }

    public function getClass(): string
    {
        return match ($this) {
            self::Success => 'alert-ctn-success',
            self::Danger => 'alert-ctn-danger',
            self::Error => 'alert-ctn-error',
            self::Info => 'alert-ctn-info',
            self::Neutral => 'alert-ctn-info',
            default => null,
        };
    }
}