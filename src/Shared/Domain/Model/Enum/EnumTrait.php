<?php

declare(strict_types=1);

namespace App\Shared\Domain\Model\Enum;

trait EnumTrait
{
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
