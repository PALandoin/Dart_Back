<?php

declare(strict_types=1);

namespace App\Scoring\Domain\Model\Enum;

use App\Shared\Domain\Model\Enum\EnumTrait;

enum MultiplicationEnum: int
{
    use EnumTrait;

    case SINGLE = 1;
    case DOUBLE = 2;
    case TRIPLE = 3;
}
