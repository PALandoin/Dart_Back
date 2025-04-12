<?php

declare(strict_types=1);

namespace App\Scoring\Application\Query\ReadScoring;

use App\Shared\Domain\Bus\Query\Query;

class ReadScoring implements Query
{
    public function __construct(
        public int $id,
    ) {
    }
}
