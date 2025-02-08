<?php

namespace App\Player\Application\Command\UpdatePlayer;

use App\Shared\Domain\Bus\Command\Command;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdatePlayer implements Command
{
    #[Ignore]
    public int $id;

    #[Assert\NotBlank(allowNull: false)]
    public string $name;
}
