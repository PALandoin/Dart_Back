<?php

namespace App\Player\Infrastructure\Controller;

use App\Player\Application\Command\UpdatePlayer\UpdatePlayer;
use App\Player\Application\Query\ReadPlayer\ReadPlayer;
use App\Shared\Infrastructure\Bus\Command\MessengerCommandBus;
use App\Shared\Infrastructure\Bus\Query\MessengerQueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/{id}', name: 'update', methods: ['PATCH'])]
readonly class UpdatePlayerController
{
    public function __construct(
        private MessengerCommandBus $commandBus,
        private MessengerQueryBus $queryBus,
    ) {
    }

    public function __invoke(#[MapRequestPayload] UpdatePlayer $updatePlayer, int $id): JsonResponse
    {
        try {
            $updatePlayer->id = $id;
            $this->commandBus->dispatch($updatePlayer);

            $getPlayer = new ReadPlayer($id);
            $player = $this->queryBus->ask($getPlayer);

            return new JsonResponse(['message' => 'Player successfully updated', 'data' => $player], 200);
        } catch (\Throwable $e) {
            return new JsonResponse(['message' => $e->getMessage()], $e->getCode() < 100 ? 500 : $e->getCode());
        }
    }
}
