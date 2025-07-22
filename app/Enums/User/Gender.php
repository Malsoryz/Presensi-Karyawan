<?php

namespace App\Enums\User;

use App\Traits\ModelEnum;

enum Gender: string {

    use ModelEnum;

    case Male = 'laki-laki';
    case Female = 'perempuan';
}