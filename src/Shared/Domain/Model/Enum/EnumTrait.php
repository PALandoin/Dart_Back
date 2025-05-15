<?php

declare(strict_types=1);

namespace App\Shared\Domain\Model\Enum;

trait EnumTrait
{
    /**
     * @return array<int|string>
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}
