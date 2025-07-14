<?php

namespace App\Enums;

enum JenisPresensi: string {
    case NONE = 'tidak ada';
    case PAGI = 'pagi';
    case SIANG = 'siang';
}