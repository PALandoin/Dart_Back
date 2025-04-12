<?php

declare(strict_types=1);

namespace App\Player\Infrastructure\Controller;

use App\Player\Application\Command\CreatePlayer\CreatePlayer;
use App\Player\Application\Query\PlayerResponse;
use App\Player\Application\Query\ReadPlayer\ReadPlayer;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Infrastructure\Service\GlobalValuesBag;

use function assert;
use function is_int;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[AsController]
#[Route('', name: 'create', methods: ['POST'], format: 'json')]
#[OA\Tag(name: 'Player')]
#[OA\Post(
    responses: [
        new OA\Response(
            response: 201,
            description: 'Player successfully created',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Player successfully created'),
                    new OA\Property(property: 'data', ref: new Model(type: PlayerResponse::class)),
                ],
            ),
        ),
    ],
)]
readonly class CreatePlayerController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {
    }

    public function __invoke(#[MapRequestPayload] CreatePlayer $createPlayer): JsonResponse
    {
        try {
            $this->commandBus->dispatch($createPlayer);

            $playerId = GlobalValuesBag::getInstance()->get('player_id');
            GlobalValuesBag::getInstance()->remove('player_id');

            assert(is_int($playerId));

            $getPlayer = new ReadPlayer($playerId);
            $player = $this->queryBus->ask($getPlayer);

            return new JsonResponse(['message' => 'Player successfully created', 'data' => $player], 201);
        } catch (Throwable $e) {
            return new JsonResponse(['message' => $e->getMessage()], $e->getCode() < 100 ? 500 : $e->getCode());
        }
    }
}
