<?php

declare(strict_types=1);

namespace App\Game\Application\Query\ReadGame;

use App\Game\Application\Query\GameResponse;
use App\Game\Domain\Event\GameRequestedEvent;
use App\Game\Domain\Repository\GameRepository;
use App\Player\Domain\Model\Player;
use App\Shared\Domain\Bus\Query\QueryHandler;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus', handles: ReadGame::class)]
readonly class ReadGameHandler implements QueryHandler
{
    public function __construct(
        private GameRepository $gameRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(ReadGame $readGame): GameResponse
    {
        $gameRequestedEvent = new GameRequestedEvent($readGame->id);

        $this->eventDispatcher->dispatch($gameRequestedEvent);

        /** @var Player $game */
        $game = $this->gameRepository->find($readGame->id);

        return GameResponse::fromModel($game);
    }
}
