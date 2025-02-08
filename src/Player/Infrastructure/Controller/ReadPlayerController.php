<?php

namespace App\Player\Infrastructure\Controller;

use App\Player\Application\Query\ReadPlayer\ReadPlayer;
use App\Shared\Infrastructure\Bus\Query\MessengerQueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('{id}', name: 'read', methods: ['GET'])]
readonly class ReadPlayerController
{
    public function __construct(private MessengerQueryBus $queryBus)
    {
    }

    public function __invoke(int $id): JsonResponse
    {
        try {
            $getPlayer = new ReadPlayer($id);

            $player = $this->queryBus->ask($getPlayer);

            return new JsonResponse(['message' => 'Player successfully read', 'data' => $player], 200);
        } catch (\Throwable $e) {
            return new JsonResponse(['message' => $e->getMessage()], $e->getCode() < 100 ? 500 : $e->getCode());
        }
    }
}
