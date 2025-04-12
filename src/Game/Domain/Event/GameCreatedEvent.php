<?php

declare(strict_types=1);

namespace App\Game\Domain\Event;

use App\Game\Domain\Model\Game;
use Symfony\Contracts\EventDispatcher\Event;

class GameCreatedEvent extends Event
{
    public Game $game;

    public function __construct(
        Game $game,
    ) {
        $this->game = $game;
    }
}
