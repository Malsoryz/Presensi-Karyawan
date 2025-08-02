<?php

namespace App\Enums\User;

use App\Traits\ModelEnum;

enum Role: string {

    use ModelEnum;

    case Karyawan = 'karyawan';
    case Admin = 'admin';
    case Magang = 'magang';
}