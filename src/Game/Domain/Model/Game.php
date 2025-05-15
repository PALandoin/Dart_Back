<?php

declare(strict_types=1);

namespace App\Game\Domain\Model;

use App\Game\Domain\Model\Enum\GameStatus;
use App\Player\Domain\Model\Player;
use App\Scoring\Domain\Model\Scoring;
use App\Shared\Domain\Model\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Game implements Model
{
    /**
     * @phpstan-ignore property.onlyRead
     */
    private int $id;

    /**
     * @var Collection<int, Player>
     */
    private Collection $players;

    private GameStatus $status = GameStatus::CREATED;

    /**
     * @var Collection<int, Scoring>
     */
    private Collection $scorings;

    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->scorings = new ArrayCollection();
    }

    /**
     * @param Player[] $playerModels
     */
    public static function create(array $playerModels): self
    {
        $game = new self();
        foreach ($playerModels as $playerModel) {
            $game->addPlayer($playerModel);
        }

        return $game;
    }

    public function addPlayer(Player $player): self
    {
        $this->players->add($player);

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function getStatus(): GameStatus
    {
        return $this->status;
    }

    public function setStatus(GameStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        $this->players->removeElement($player);

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
        $this->scorings->add($scoring);

        return $this;
    }

    public function removeScoring(Scoring $scoring): self
    {
        $this->scorings->removeElement($scoring);

        return $this;
    }

    public function getPlayerScore(Player $player): int
    {
        $score = 0;
        foreach ($this->scorings as $scoring) {
            if ($scoring->getPlayer() === $player) {
                foreach ($scoring->getDartThrows() as $dartThrow) {
                    $score += $dartThrow->getScore();
                }
            }
        }

        return $score;
    }
}
