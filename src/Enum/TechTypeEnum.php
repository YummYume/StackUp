<?php

namespace App\Enum;

use App\Enum\Traits\UtilsTrait;

enum TechTypeEnum: string
{
    use UtilsTrait;

    case Language = 'language';
    case Library = 'library';
    case Tool = 'tool';
}
