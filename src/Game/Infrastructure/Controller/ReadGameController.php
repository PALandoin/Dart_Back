<?php

namespace App\Game\Infrastructure\Controller;

use App\Game\Application\Query\GameResponse;
use App\Game\Application\Query\ReadGame\ReadGame;
use App\Shared\Domain\Bus\Query\QueryBus;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/{id}', name: 'read', requirements: ['id' => '\d+'], methods: ['GET'], format: 'json')]
#[OA\Tag(name: 'Game')]
#[OA\Get(
    responses: [
        new OA\Response(
            response: 200,
            description: 'Game successfully read',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Game successfully read'),
                    new OA\Property(property: 'data', ref: new Model(type: GameResponse::class)),
                ],
            )
        ),
    ]
)]
readonly class ReadGameController
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function __invoke(int $id): JsonResponse
    {
        try {
            $readGame = new ReadGame($id);
            $game = $this->queryBus->ask($readGame);

            return new JsonResponse(['message' => 'Game successfully read', 'data' => $game], 200);
        } catch (\Throwable $e) {
            return new JsonResponse(['message' => $e->getMessage()], $e->getCode() < 100 ? 500 : $e->getCode());
        }
    }
}
