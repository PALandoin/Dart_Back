<?php

declare(strict_types=1);

namespace App\Player\Infrastructure\EventListener;

use App\Player\Domain\Event\PlayerCreatedEvent;
use App\Player\Domain\Repository\PlayerRepository;
use App\Shared\Infrastructure\Service\GlobalValuesBag;

use function count;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsEventListener(event: PlayerCreatedEvent::class, method: '__invoke')]
readonly class OnPlayerCreated
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(PlayerCreatedEvent $event): void
    {
        try {
            $this->entityManager->beginTransaction();

            $player = $event->player;

            $error = $this->validator->validate($player);

            if (count($error) > 0) {
                throw new UnprocessableEntityHttpException(message: $error, code: 422);
            }

            $this->playerRepository->save($event->player);

            GlobalValuesBag::getInstance()->set('player_id', $event->player->getId());

            $this->entityManager->commit();
        } catch (Exception $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}
