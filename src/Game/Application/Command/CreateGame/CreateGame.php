<?php

namespace App\Game\Application\Command\CreateGame;

use App\Shared\Domain\Bus\Command\Command;
use Symfony\Component\Validator\Constraints as Assert;

class CreateGame implements Command
{
    public function __construct(
        /**
         * @var int[]
         */
        #[Assert\Count(min: 2, max: 2)]
        #[Assert\All(
            constraints: [
                new Assert\Type(type: 'int'),
            ]
        )]
        public array $players,
    ) {
    }
}
