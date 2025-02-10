<?php

namespace App\Player\Infrastructure\Controller;

use App\Player\Application\Query\PlayerResponse;
use App\Player\Application\Query\ReadPlayer\ReadPlayer;
use App\Shared\Domain\Bus\Query\QueryBus;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('{id}', name: 'read', requirements: ['id' => '\d+'], methods: ['GET'], format: 'json')]
#[OA\Tag(name: 'Player')]
#[OA\Get(responses: [
    new OA\Response(
        response: 200,
        description: 'Player successfully read',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Player successfully read'),
                new OA\Property(property: 'data', ref: new Model(type: PlayerResponse::class)),
            ],
        )
    ),
])]
readonly class ReadPlayerController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
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
