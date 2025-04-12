<?php

declare(strict_types=1);

namespace App\Game\Domain\Repository;

use App\Game\Domain\Model\Game;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 */
interface GameRepository
{
    public function save(Game $game): void;
}
