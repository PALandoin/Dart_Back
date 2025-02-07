<?php

namespace App\Player\Application\Query\GetPlayer;

use App\Shared\Domain\Bus\Query\Query;

class GetPlayer implements Query
{
    public function __construct(
        public int $id,
    ) {
    }
}
