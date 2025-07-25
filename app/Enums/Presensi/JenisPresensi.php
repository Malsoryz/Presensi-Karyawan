<?php

namespace App\Enums\Presensi;

use App\Traits\ModelEnum;

enum JenisPresensi: string {

    use ModelEnum;

    case Pagi = 'pagi';
    case Siang = 'siang';
}