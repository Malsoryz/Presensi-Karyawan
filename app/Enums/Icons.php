<?php

namespace App\Enums;

use Illuminate\View\ComponentAttributeBag;

enum Icons: string {
    case Info = 'info';
    case Check = 'check';
    case Danger = 'danger';
    case Error = 'error';

    public function render($extraClass = null)
    {
        $defaultClass = 'w-24 h-24';
        $class = "$defaultClass $extraClass";

        $attribute = fn (string $color) => new ComponentAttributeBag([
            'class' => "$color $class"
        ]);

        return match ($this) {
            Icons::Info => view('e60dd9d2c3a62d619c9acb38f20d5aa5::icon.information-circle', [
                'attributes' => $attribute(Icons::Info->color()),
            ])->render(),
            Icons::Check => view('e60dd9d2c3a62d619c9acb38f20d5aa5::icon.check-circle', [
                'attributes' => $attribute(Icons::Check->color()),
            ])->render(),
            Icons::Danger => view('e60dd9d2c3a62d619c9acb38f20d5aa5::icon.exclamation-triangle', [
                'attributes' => $attribute(Icons::Danger->color()),
            ])->render(),
            Icons::Error => view('e60dd9d2c3a62d619c9acb38f20d5aa5::icon.exclamation-circle', [
                'attributes' => $attribute(Icons::Error->color()),
            ])->render(),
        };
    }

    private function color(): string
    {
        return match ($this) {
            Icons::Info => 'text-blue-400',
            Icons::Check => 'text-green-400',
            Icons::Danger => 'text-yellow-400',
            Icons::Error => 'text-red-400',
        };
    }
}