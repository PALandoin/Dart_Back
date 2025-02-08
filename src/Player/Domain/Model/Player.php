<?php

namespace App\Player\Domain\Model;

use App\Shared\Domain\Model\Model;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity('name')]
class Player implements Model
{
    /**
     * @phpstan-ignore property.onlyRead
     */
    private int $id;

    public function __construct(
        private string $name,
    ) {
    }

    public static function register(string $name): self
    {
        return new self($name);
    }

    public function update(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
