<?php

namespace App\Player\Domain\Event;

use App\Player\Domain\Model\Player;
use Symfony\Contracts\EventDispatcher\Event;

class PlayerUpdatedEvent extends Event
{
    public function __construct(
        public Player $player,
    ) {
    }
}
