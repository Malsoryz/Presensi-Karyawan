<?php

namespace App\Enums\User;

use App\Traits\ModelEnum;

enum Tipe: string {

    use ModelEnum;

    case Pekerja = 'pekerja tetap';
    case Magang = 'magang';
}