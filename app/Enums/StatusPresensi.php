<?php

namespace App\Enums;

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
}