<?php

namespace App\Scoring\Application\Query;

use App\Game\Application\Query\GameResponse;
use App\Player\Application\Query\PlayerResponse;
use App\Scoring\Domain\Model\Scoring;
use App\Shared\Domain\Bus\Query\Response;
use App\Shared\Domain\Exception\UnexpectedModelException;
use App\Shared\Domain\Model\Model;

class ScoringResponse implements Response
{
    public int $id;
    public GameResponse $game;
    public PlayerResponse $player;
    public int $score;

    public static function fromModel(Model $model): self
    {
        if (!$model instanceof Scoring) {
            throw new UnexpectedModelException($model, Scoring::class);
        }

        $response = new self();
        $response->id = $model->getId();
        $response->game = GameResponse::fromModel($model->getGame());
        $response->player = PlayerResponse::fromModel($model->getPlayer());
        $response->score = $model->getScore();

        return $response;
    }
}
