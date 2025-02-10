<?php

namespace App\Game\Application\Command\CreateGame;

use App\Game\Domain\Event\GameCreatedEvent;
use App\Game\Domain\Model\Game;
use App\Player\Domain\Event\PlayerRequestedEvent;
use App\Player\Domain\Repository\PlayerRepository;
use App\Shared\Domain\Bus\Command\CommandHandler;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus', handles: CreateGame::class)]
readonly class CreateGameHandler implements CommandHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private PlayerRepository $playerRepository,
    ) {
    }

    public function __invoke(CreateGame $createGame): void
    {
        $playerModels = [];
        foreach ($createGame->players as $player) {
            $playerRequestedEvent = new PlayerRequestedEvent($player);

            $this->eventDispatcher->dispatch($playerRequestedEvent);

            $playerModels[] = $this->playerRepository->find($player);
        }

        $game = Game::create($playerModels);

        $this->eventDispatcher->dispatch(new GameCreatedEvent($game));
    }
}
