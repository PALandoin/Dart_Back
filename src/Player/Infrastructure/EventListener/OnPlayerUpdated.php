<?php

declare(strict_types=1);

namespace App\Player\Infrastructure\EventListener;

use App\Player\Domain\Event\PlayerUpdatedEvent;
use App\Player\Domain\Repository\PlayerRepository;

use function count;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsEventListener(event: PlayerUpdatedEvent::class, method: 'onPlayerUpdated')]
readonly class OnPlayerUpdated
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private PlayerRepository $playerRepository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function onPlayerUpdated(PlayerUpdatedEvent $event): void
    {
        try {
            $this->entityManager->beginTransaction();

            $player = $event->player;

            $error = $this->validator->validate($player);

            if (count($error) > 0) {
                throw new UnprocessableEntityHttpException(message: $error, code: 422);
            }

            $this->playerRepository->save($event->player);

            $this->entityManager->commit();
        } catch (Exception $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}
