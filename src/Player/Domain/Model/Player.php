<?php

namespace App\Player\Domain\Model;

use App\Game\Domain\Model\Game;
use App\Shared\Domain\Model\Model;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[UniqueEntity('name')]
class Player implements Model
{
    /**
     * @phpstan-ignore property.onlyRead
     */
    private int $id;

    private string $name;

    /**
     * @var Game[]
     */
    private array $games;

    public static function register(string $name): self
    {
        $player = new self();
        $player->setName($name);

        return $player;
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Game[]
     */
    public function getGames(): array
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        $this->games[] = $game;

        return $this;
    }
}
