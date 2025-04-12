<?php

declare(strict_types=1);

namespace App\Scoring\Application\Command\CreateScoring;

use App\Scoring\Application\Command\Subresource\CreateDartThrow;
use App\Shared\Domain\Bus\Command\Command;
use Symfony\Component\Validator\Constraints as Assert;

class CreateScoring implements Command
{
    public function __construct(
        public int $gameId,
        public int $playerId,
        #[Assert\Valid]
        public CreateDartThrow $dartThrow,
    ) {
    }
}
