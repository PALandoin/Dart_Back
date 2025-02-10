<?php

namespace App\Game\Application\Query\ReadGame;

use App\Shared\Domain\Bus\Query\Query;

class ReadGame implements Query
{
    public function __construct(
        public int $id,
    ) {
    }
}
