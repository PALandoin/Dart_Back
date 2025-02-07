<?php

namespace App\Player\Application\Query;

use App\Player\Domain\Model\Player;
use App\Shared\Domain\Bus\Query\Response;
use App\Shared\Domain\Exception\UnexpectedModelException;
use App\Shared\Domain\Model\Model;

class PlayerResponse implements Response
{
    public int $id;
    public string $name;

    public static function fromModel(Model $model): self
    {
        if (!$model instanceof Player) {
            throw new UnexpectedModelException($model, Player::class);
        }

        $response = new self();
        $response->id = $model->getId();
        $response->name = $model->getName();

        return $response;
    }
}
