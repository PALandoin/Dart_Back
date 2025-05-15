<?php

declare(strict_types=1);

namespace App\Scoring\Infrastructure\Controller;

use App\Scoring\Application\Command\CreateScoring\CreateScoring;
use App\Scoring\Application\Query\ReadScoring\ReadScoring;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Infrastructure\Service\GlobalValuesBag;

use function assert;

use Exception;

use function is_int;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('', name: 'create', methods: ['POST'], format: 'json')]
#[OA\Tag(name: 'Scoring')]
#[OA\Post(
    responses: [
        new OA\Response(
            response: 201,
            description: 'Scoring successfully created',
            content: [
                new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Scoring successfully created'),
                        // new OA\Property(property: 'data', ref: ScoringResponse::class),
                    ],
                ),
            ],
        ),
    ],
)]
readonly class CreateScoringController
{
    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
    ) {
    }

    public function __invoke(#[MapRequestPayload] CreateScoring $createScoring): JsonResponse
    {
        try {
            $this->commandBus->dispatch($createScoring);

            $scoringId = GlobalValuesBag::getInstance()->get('scoring_id');
            GlobalValuesBag::getInstance()->remove('scoring_id');

            assert(is_int($scoringId));

            $getScoring = new ReadScoring($scoringId);
            $scoringResponse = $this->queryBus->ask($getScoring);

            return new JsonResponse([
                'message' => 'Scoring successfully created',
                'data' => $scoringResponse,
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], $e->getCode() < 100 ? 500 : $e->getCode());
        }
    }
}
