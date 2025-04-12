<?php

declare(strict_types=1);

namespace App\Scoring\Domain\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ScoringRequestedEvent extends Event
{
    public int $id;

    public function __construct(
        int $id,
    ) {
        $this->id = $id;
    }
}
