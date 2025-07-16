<?php

namespace App\Enums;

enum SesiPresensi: string {
    case LIBUR = 'libur';
    case BELUM_MULAI = 'belum mulai';
    case SESI_PRESENSI = 'sesi presensi';
    case SELESAI = 'selesai';

    public function message(): string
    {
        return match ($this) {
            SesiPresensi::LIBUR => 'Tidak ada presensi hari ini karena libur.',
            SesiPresensi::BELUM_MULAI => 'Sesi presensi belum dimulai.',
            SesiPresensi::SESI_PRESENSI => 'Sesi presensi sedang berlangsung.',
            SesiPresensi::SELESAI => 'Sesi presensi telah selesai.',
        };
    }
}