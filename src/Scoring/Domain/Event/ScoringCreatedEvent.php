<?php

namespace App\Scoring\Domain\Event;

use App\Scoring\Domain\Model\Scoring;
use Symfony\Contracts\EventDispatcher\Event;

class ScoringCreatedEvent extends Event
{
    public Scoring $scoring;

    public function __construct(
        Scoring $scoring,
    ) {
        $this->scoring = $scoring;
    }
}
