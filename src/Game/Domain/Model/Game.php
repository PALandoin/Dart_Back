<?php

namespace App\Game\Domain\Model;

use App\Game\Domain\Model\Enum\GameStatus;
use App\Player\Domain\Model\Player;
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

    public function __construct()
    {
        $this->players = new ArrayCollection();
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
}
