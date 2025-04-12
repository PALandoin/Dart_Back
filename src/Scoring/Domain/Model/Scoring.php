<?php

namespace App\Scoring\Domain\Model;

use App\Game\Domain\Model\Game;
use App\Player\Domain\Model\Player;
use App\Shared\Domain\Model\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

class Scoring implements Model
{
    /**
     * @phpstan-ignore property.onlyRead
     */
    private int $id;

    /**
     * @var Collection<int, DartThrow>
     */
    #[Assert\Count(min: 1, max: 3)]
    private Collection $dartThrows;

    private Game $game;

    private Player $player;

    public function __construct(
    ) {
        $this->dartThrows = new ArrayCollection();
    }

    public static function create(Game $game, Player $player): self
    {
        $scoring = new self();
        $scoring->game = $game;
        $scoring->player = $player;

        return $scoring;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, DartThrow>
     */
    public function getDartThrows(): Collection
    {
        return $this->dartThrows;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function setGame(Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function setPlayer(Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function addDartThrow(DartThrow $dartThrow): self
    {
        $this->dartThrows->add($dartThrow);

        return $this;
    }

    public function removeDartThrow(DartThrow $dartThrow): self
    {
        $this->dartThrows->removeElement($dartThrow);

        return $this;
    }

    public function getScore(): int
    {
        $score = 0;
        foreach ($this->dartThrows as $dartThrow) {
            $score += $dartThrow->getScore();
        }

        return $score;
    }
}
