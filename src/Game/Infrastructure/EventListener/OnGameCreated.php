<?php

namespace App\Game\Infrastructure\EventListener;

use App\Game\Domain\Event\GameCreatedEvent;
use App\Game\Domain\Repository\GameRepository;
use App\Shared\Infrastructure\Service\GlobalValuesBag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsEventListener(event: GameCreatedEvent::class, method: 'onGameCreated')]
readonly class OnGameCreated
{
    public function __construct(
        private GameRepository $gameRepository,
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function onGameCreated(GameCreatedEvent $event): void
    {
        try {
            $this->entityManager->beginTransaction();

            $game = $event->game;

            $error = $this->validator->validate($game);

            if (count($error) > 0) {
                throw new UnprocessableEntityHttpException(message: $error, code: 422);
            }

            $this->gameRepository->save($event->game);

            GlobalValuesBag::getInstance()->set('game_id', $event->game->getId());

            $this->entityManager->commit();
        } catch (\Exception $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}
