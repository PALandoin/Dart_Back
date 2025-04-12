<?php

declare(strict_types=1);

namespace App\Game\Infrastructure\Controller;

use App\Game\Application\Command\CreateGame\CreateGame;
use App\Game\Application\Query\GameResponse;
use App\Game\Application\Query\ReadGame\ReadGame;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Infrastructure\Service\GlobalValuesBag;

use function assert;
use function is_int;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[AsController]
#[Route('', name: 'create', methods: ['POST'], format: 'json')]
#[OA\Tag(name: 'Game')]
#[OA\Post(
    responses: [
        new OA\Response(
            response: 201,
            description: 'Game successfully created',
            content: [
                new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Game successfully created'),
                        new OA\Property(property: 'data', ref: GameResponse::class),
                    ],
                ),
            ],
        ),
    ],
)]
readonly class CreateGameController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {
    }

    public function __invoke(#[MapRequestPayload] CreateGame $game): JsonResponse
    {
        try {
            $this->commandBus->dispatch($game);

            $gameId = GlobalValuesBag::getInstance()->get('game_id');
            GlobalValuesBag::getInstance()->remove('game_id');

            assert(is_int($gameId));

            $getGame = new ReadGame($gameId);
            $game = $this->queryBus->ask($getGame);

            return new JsonResponse(['message' => 'Game successfully created', 'data' => $game], 201);
        } catch (Throwable $e) {
            return new JsonResponse(['message' => $e->getMessage()], $e->getCode() < 100 ? 500 : $e->getCode());
        }
    }
}
