<?php

namespace App\Player\Application\Command\CreatePlayer;

use App\Shared\Domain\Bus\Command\Command;
use Symfony\Component\Validator\Constraints as Assert;

readonly class CreatePlayer implements Command
{
    public function __construct(
        #[Assert\NotBlank(allowNull: false)]
        public string $name,
    )
    {
    }
}
