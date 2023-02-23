<?php

namespace App\Enum;

use App\Enum\Traits\UtilsTrait;

enum RequestStatusEnum: string
{
    use UtilsTrait;

    case Rejected = 'rejected';
    case Pending = 'pending';
    case Accepted = 'accepted';
}
