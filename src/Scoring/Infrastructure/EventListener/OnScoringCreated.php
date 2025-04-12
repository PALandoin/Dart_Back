<?php

declare(strict_types=1);

namespace App\Scoring\Infrastructure\EventListener;

use App\Game\Domain\Model\Enum\GameStatus;
use App\Scoring\Domain\Event\ScoringCreatedEvent;
use App\Scoring\Domain\Repository\ScoringRepository;
use App\Shared\Infrastructure\Service\GlobalValuesBag;

use function count;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsEventListener(event: ScoringCreatedEvent::class, method: 'onScoringCreated')]
readonly class OnScoringCreated
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private ScoringRepository $scoringRepository,
    ) {
    }

    public function onScoringCreated(ScoringCreatedEvent $event): void
    {
        try {
            $this->entityManager->beginTransaction();

            $scoring = $event->scoring;

            $error = $this->validator->validate($scoring);

            if (count($error) > 0) {
                throw new UnprocessableEntityHttpException(message: $error, code: 422);
            }

            if ($scoring->getGame()->getStatus() === GameStatus::FINISHED) {
                throw new UnprocessableEntityHttpException(message: 'Game is finished', code: 422);
            }

            $this->scoringRepository->save($event->scoring);

            GlobalValuesBag::getInstance()->set('scoring_id', $event->scoring->getId());

            $this->entityManager->commit();
        } catch (Exception $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}
