<?php

namespace App\Player\Application\Query\GetPlayer;

use App\Player\Application\Query\PlayerResponse;
use App\Player\Domain\Repository\PlayerRepository;
use App\Shared\Domain\Bus\Query\QueryHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus', handles: GetPlayer::class)]
readonly class GetPlayerHandler implements QueryHandler
{
    public function __construct(
        private PlayerRepository $playerRepository,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(GetPlayer $getPlayer): PlayerResponse
    {
        $model = $this->playerRepository->find($getPlayer->id);

        if (null === $model) {
            throw new \Exception('Player not found', 404);
        }

        return PlayerResponse::fromModel($model);
    }
}
