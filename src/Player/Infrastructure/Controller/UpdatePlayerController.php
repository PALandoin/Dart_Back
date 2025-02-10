<?php

namespace App\Player\Infrastructure\Controller;

use App\Player\Application\Command\UpdatePlayer\UpdatePlayer;
use App\Player\Application\Query\PlayerResponse;
use App\Player\Application\Query\ReadPlayer\ReadPlayer;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\Bus\Query\QueryBus;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/{id}', name: 'update', requirements: ['id' => '\d+'], methods: ['PATCH'], format: 'json')]
#[OA\Tag(name: 'Player')]
#[OA\Patch(responses: [
    new OA\Response(
        response: 200,
        description: 'Player successfully updated',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'message', type: 'string', example: 'Player successfully updated'),
                new OA\Property(property: 'data', ref: new Model(type: PlayerResponse::class)),
            ],
        )
    ),
])]
readonly class UpdatePlayerController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
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
