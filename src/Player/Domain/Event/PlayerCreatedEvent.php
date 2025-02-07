<?php

namespace App\Player\Domain\Event;

use App\Player\Domain\Model\Player;
use Symfony\Contracts\EventDispatcher\Event;

class PlayerCreatedEvent extends Event
{
    public Player $player;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }
}
