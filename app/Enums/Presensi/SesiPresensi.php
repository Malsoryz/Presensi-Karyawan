<?php

namespace App\Enums\Presensi;

use App\Models\HariLibur as HL;
use App\Models\Config as CFG;

enum SesiPresensi: int {
    case Libur = 2;
    case None = 0;
    case SesiPresensi = 1;

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
            SesiPresensi::Libur => $liburMessage,
            SesiPresensi::None => 'Tidak ada sesi presensi.',
            SesiPresensi::SesiPresensi => 'Sesi presensi sedang berlangsung.',
        };
    }

    public function isSession(): bool
    {
        return match ($this) {
            SesiPresensi::SesiPresensi => true,
            default => false,
        };
    }
}