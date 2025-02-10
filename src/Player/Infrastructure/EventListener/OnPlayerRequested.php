<?php

namespace App\Player\Infrastructure\EventListener;

use App\Player\Domain\Event\PlayerRequestedEvent;
use App\Player\Domain\Repository\PlayerRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsEventListener(event: PlayerRequestedEvent::class, method: 'onPlayerRequested')]
readonly class OnPlayerRequested
{
    public function __construct(
        private PlayerRepository $playerRepository,
    ) {
    }

    public function onPlayerRequested(PlayerRequestedEvent $event): void
    {
        $player = $this->playerRepository->find($event->id);

        if (null === $player) {
            throw new NotFoundHttpException('Player with id '.$event->id.' not found', code: 404);
        }
    }
}
