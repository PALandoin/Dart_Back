<?php

namespace App\Player\Infrastructure\Controller;

use App\Player\Application\Command\CreatePlayer\CreatePlayer;
use App\Player\Application\Query\GetPlayer\GetPlayer;
use App\Shared\Infrastructure\Bus\Command\MessengerCommandBus;
use App\Shared\Infrastructure\Bus\Query\MessengerQueryBus;
use App\Shared\Infrastructure\Service\GlobalValuesBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('', name: 'create', methods: ['POST'])]
readonly class CreatePlayerController
{
    public function __construct(
        private MessengerCommandBus $commandBus,
        private MessengerQueryBus   $queryBus,
    )
    {
    }

    public function __invoke(#[MapRequestPayload] CreatePlayer $createPlayer, Request $request): JsonResponse
    {
        try {
            $this->commandBus->dispatch($createPlayer);

            $playerId = GlobalValuesBag::getInstance()->get('player_id');

            $getPlayer = new GetPlayer($playerId);
            $player = $this->queryBus->ask($getPlayer);

            return new JsonResponse(['message' => 'Player successfully created', 'data' => $player], 201);
        } catch (\Throwable $e) {
            return new JsonResponse(['message' => $e->getMessage()], $e->getCode() < 100 ? 500 : $e->getCode());
        }
    }
}
