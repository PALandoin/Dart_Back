<?php

namespace App\Scoring\Application\Query\ReadScoring;

use App\Scoring\Application\Query\ScoringResponse;
use App\Scoring\Domain\Event\ScoringRequestedEvent;
use App\Scoring\Domain\Model\Scoring;
use App\Scoring\Domain\Repository\ScoringRepository;
use App\Shared\Domain\Bus\Query\QueryHandler;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus', handles: ReadScoring::class)]
readonly class ReadScoringHandler implements QueryHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private ScoringRepository $scoringRepository,
    ) {
    }

    public function __invoke(ReadScoring $readScoring): ScoringResponse
    {
        $scoringRequest = new ScoringRequestedEvent($readScoring->id);

        $this->eventDispatcher->dispatch($scoringRequest);

        /** @var Scoring $scoring */
        $scoring = $this->scoringRepository->find($readScoring->id);

        return ScoringResponse::fromModel($scoring);
    }
}
