<?php

namespace App\Player\Domain\Model;

use App\Game\Domain\Model\Game;
use App\Scoring\Domain\Model\Scoring;
use App\Shared\Domain\Model\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @var Collection<int, Game>
     */
    private Collection $games;

    /**
     * @return Collection<int, Scoring>
     */
    private Collection $scorings;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->scorings = new ArrayCollection();
    }

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
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        $this->games[] = $game;

        return $this;
    }

    public function removeGame(Game $game): self
    {
        $this->games->removeElement($game);

        return $this;
    }

    /**
     * @return Collection<int, Scoring>
     */
    public function getScorings(): Collection
    {
        return $this->scorings;
    }

    public function addScoring(Scoring $scoring): self
    {
        $this->scorings[] = $scoring;

        return $this;
    }

    public function removeScoring(Scoring $scoring): self
    {
        $this->scorings->removeElement($scoring);

        return $this;
    }
}
