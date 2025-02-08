<?php

namespace App\Player\Domain\Event;

use Symfony\Contracts\EventDispatcher\Event;

class PlayerRequestedEvent extends Event
{
    public int $id;

    public function __construct(
        int $id,
    ) {
        $this->id = $id;
    }
}
