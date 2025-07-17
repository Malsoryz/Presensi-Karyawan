<?php

namespace App\Enums;

use App\Models\HariLibur as HL;
use App\Models\Config as CFG;

enum SesiPresensi: string {
    case LIBUR = 'libur';
    case BELUM_MULAI = 'belum mulai';
    case SESI_PRESENSI = 'sesi presensi';
    case SELESAI = 'selesai';

    public function message(): string
    {
        $timezone = CFG::get('timezone', 'Asia/Makassar');
        $today = now($timezone)->toDateString();
        $hariLibur = HL::whereDate('tanggal', $today)->first();
        $isHariLibur = HL::whereDate('tanggal', $today)->exists();
        $liburMessage = $isHariLibur ? 
            "Tidak ada presensi hari ini karena libur <strong>$hariLibur->nama</strong>":
            "Tidak ada presensi hari ini karena hari <strong>minggu.</strong>";

        return match ($this) {
            SesiPresensi::LIBUR => $liburMessage,
            SesiPresensi::BELUM_MULAI => 'Sesi presensi belum dimulai.',
            SesiPresensi::SESI_PRESENSI => 'Sesi presensi sedang berlangsung.',
            SesiPresensi::SELESAI => 'Sesi presensi telah selesai.',
        };
    }
}