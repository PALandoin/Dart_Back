<?php

declare(strict_types=1);

namespace App\Player\Application\Command\CreatePlayer;

use App\Shared\Domain\Bus\Command\Command;
use Symfony\Component\Validator\Constraints as Assert;

final class CreatePlayer implements Command
{
    public function __construct(
        #[Assert\NotBlank(allowNull: false)]
        public string $name,
    ) {
    }
}
