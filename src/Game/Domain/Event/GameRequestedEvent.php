<?php

namespace App\Game\Domain\Event;

use Symfony\Contracts\EventDispatcher\Event;

class GameRequestedEvent extends Event
{
    public int $id;

    public function __construct(
        int $id,
    ) {
        $this->id = $id;
    }
}
