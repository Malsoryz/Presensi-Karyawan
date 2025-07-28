<?php

namespace App\Enums\Presensi;

use App\Enums\Icons;
use App\Traits\ModelEnum;

enum StatusPresensi: string {

    use ModelEnum;

    case Masuk = 'masuk';
    case Terlambat = 'terlambat';
    case Ijin = 'ijin';
    case Sakit = 'sakit';
    case TidakMasuk = 'tidak_masuk';

    public function message(): string
    {
        return match ($this) {
            StatusPresensi::Masuk => 'Anda telah melakukan presensi.',
            StatusPresensi::Terlambat => 'Anda telah melakukan presensi walaupun anda terlambat.',
            StatusPresensi::Ijin => 'Anda telah diberikan ijin untuk tidak masuk kerja.',
            StatusPresensi::Sakit => 'Anda telah diberikan ijin untuk tidak masuk kerja karena sakit.',
            StatusPresensi::TidakMasuk => 'Anda tidak melakukan presensi dan tidak masuk kerja. kenapa?',
        };
    }

    public function icon($extraClass = null)
    {
        return match ($this) {
            StatusPresensi::Masuk,
            StatusPresensi::Terlambat => Icons::Check->render($extraClass),
            StatusPresensi::Ijin,
            StatusPresensi::Sakit => Icons::Info->render($extraClass),
            StatusPresensi::TidakMasuk => Icons::Error->render($extraClass),
        };
    }
}