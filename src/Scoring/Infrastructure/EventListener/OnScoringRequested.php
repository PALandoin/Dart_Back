<?php

namespace App\Scoring\Infrastructure\EventListener;

use App\Scoring\Domain\Event\ScoringRequestedEvent;
use App\Scoring\Domain\Repository\ScoringRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsEventListener(event: ScoringRequestedEvent::class, method: 'onScoringRequested')]
readonly class OnScoringRequested
{
    public function __construct(
        private ScoringRepository $scoringRepository,
    ) {
    }

    public function onScoringRequested(ScoringRequestedEvent $event): void
    {
        $scoring = $this->scoringRepository->find($event->id);

        if (null === $scoring) {
            throw new NotFoundHttpException('Scoring with id '.$event->id.' not found', code: 404);
        }
    }
}
