<?php

namespace App\Game\Infrastructure\EventListener;

use App\Game\Domain\Event\GameRequestedEvent;
use App\Game\Domain\Repository\GameRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsEventListener(event: GameRequestedEvent::class, method: 'onGameRequested')]
readonly class OnGameRequested
{
    public function __construct(private GameRepository $gameRepository)
    {
    }

    public function onGameRequested(GameRequestedEvent $event): void
    {
        $player = $this->gameRepository->find($event->id);

        if (null === $player) {
            throw new NotFoundHttpException('Game with id '.$event->id.' not found', code: 404);
        }
    }
}
