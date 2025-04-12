<?php

declare(strict_types=1);

namespace App\Game\Domain\Event;

use Symfony\Contracts\EventDispatcher\Event;

class GameRequestedEvent extends Event
{
    public int $id;
    public ?int $playerId;

    public function __construct(
        int $id,
        ?int $playerId = null,
    ) {
        $this->id = $id;
        $this->playerId = $playerId;
    }
}
