<?php

namespace App\Enums\Notification;

use App\Traits\ModelEnum;

enum Type: string {
    use ModelEnum;

    case Approval = 'approval';
    case Etc = 'etc';
}