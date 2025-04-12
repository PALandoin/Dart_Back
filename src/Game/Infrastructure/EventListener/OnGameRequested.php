<?php

declare(strict_types=1);

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
        $game = $this->gameRepository->find($event->id);

        if ($game === null) {
            throw new NotFoundHttpException('Game with id '.$event->id.' not found', code: 404);
        }

        if ($event->playerId !== null && $game->getPlayers()->filter(fn ($player) => $player->getId() === $event->playerId)->isEmpty()) {
            throw new NotFoundHttpException('Game with id '.$event->id.' not found for player with id '.$event->playerId, code: 422);
        }
    }
}
