<?php

namespace App\Enums;

enum StatusPresensi: string {
    case MASUK = 'masuk';
    case TERLAMBAT = 'terlambat';
    case IJIN = 'ijin';
    case SAKIT = 'sakit';
    case TIDAK_MASUK = 'tidak_masuk';
}