<?php

namespace App\Enums;

use Illuminate\View\ComponentAttributeBag;

enum StatusPresensi: string {
    case BELUM = 'belum';
    case MASUK = 'masuk';
    case TERLAMBAT = 'terlambat';
    case IJIN = 'ijin';
    case SAKIT = 'sakit';
    case TIDAK_MASUK = 'tidak_masuk';

    public function message(): string
    {
        return match ($this) {
            StatusPresensi::BELUM => 'Anda belum melakukan presensi.',
            StatusPresensi::MASUK => 'Anda telah melakukan presensi.',
            StatusPresensi::TERLAMBAT => 'Anda telah melakukan presensi walaupun anda terlambat.',
            StatusPresensi::IJIN => 'Anda telah diberikan ijin untuk tidak masuk kerja.',
            StatusPresensi::SAKIT => 'Anda telah diberikan ijin untuk tidak masuk kerja karena sakit.',
            StatusPresensi::TIDAK_MASUK => 'Anda tidak melakukan presensi dan tidak masuk kerja. kenapa?',
        };
    }

    public function icon($extraClass = null): string
    {
        $defaultClass = 'w-24 h-24';
        $class = "$defaultClass $extraClass";
        return match ($this) {
            StatusPresensi::MASUK, 
            StatusPresensi::TERLAMBAT => view('e60dd9d2c3a62d619c9acb38f20d5aa5::icon.check-circle', [
                'attributes' => new ComponentAttributeBag(['class' => "text-green-400 $class"])
            ])->render(),
            StatusPresensi::BELUM, 
            StatusPresensi::IJIN, 
            StatusPresensi::SAKIT => view('e60dd9d2c3a62d619c9acb38f20d5aa5::icon.information-circle', [
                'attributes' => new ComponentAttributeBag(['class' => "text-blue-400 $class"])
            ])->render(),
            StatusPresensi::TIDAK_MASUK => view('e60dd9d2c3a62d619c9acb38f20d5aa5::icon.exclamation-circle', [
                'attributes' => new ComponentAttributeBag(['class' => "text-red-400 $class"])
            ])->render(),
        };
    }
}