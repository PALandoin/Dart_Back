<?php

declare(strict_types=1);

namespace App\Player\Application\Query\ReadPlayer;

use App\Shared\Domain\Bus\Query\Query;

final class ReadPlayer implements Query
{
    public function __construct(
        public int $id,
    ) {
    }
}
