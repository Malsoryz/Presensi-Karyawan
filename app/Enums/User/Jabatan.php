<?php

namespace App\Enums\User;

use App\Traits\ModelEnum;

enum Jabatan: string {

    use ModelEnum;

    case Senior = 'programmer senior';
    case Junior = 'programmer junior';
    case Hr = 'admin';
}