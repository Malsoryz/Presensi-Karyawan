<?php

namespace App\Enums;

enum SesiPresensi: string {
    case LIBUR = 'libur';
    case BELUM_MULAI = 'belum mulai';
    case SESI_PRESENSI = 'sesi presensi';
    case SELESAI = 'selesai';
}