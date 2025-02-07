<?php

namespace App\Player\Domain\Repository;

use App\Player\Domain\Model\Player;

/**
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 */
interface PlayerRepository
{
    public function save(Player $player): void;
}
