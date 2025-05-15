<?php

declare(strict_types=1);

namespace App\Game\Application\Query;

use App\Game\Domain\Model\Enum\GameStatus;
use App\Game\Domain\Model\Game;
use App\Player\Application\Query\PlayerResponse;
use App\Shared\Domain\Bus\Query\Response;
use App\Shared\Domain\Exception\UnexpectedModelException;
use App\Shared\Domain\Model\Model;

class GameResponse implements Response
{
    public int $id;
    public GameStatus $status;
    /**
     * @var PlayerResponse[]
     */
    public array $players;
    /**
     * @var array<int, int>
     */
    public array $scores;

    public static function fromModel(Model $model): self
    {
        if (!$model instanceof Game) {
            throw new UnexpectedModelException($model, Game::class);
        }

        $response = new self();
        $response->id = $model->getId();
        $response->status = $model->getStatus();

        foreach ($model->getPlayers() as $player) {
            $response->players[] = PlayerResponse::fromModel($player);
            $response->scores[$player->getId()] = $model->getPlayerScore($player);
        }

        return $response;
    }
}
