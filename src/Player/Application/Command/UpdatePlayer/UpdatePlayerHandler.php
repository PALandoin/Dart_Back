<?php

namespace App\Player\Application\Command\UpdatePlayer;

use App\Player\Domain\Event\PlayerRequestedEvent;
use App\Player\Domain\Event\PlayerUpdatedEvent;
use App\Player\Infrastructure\Doctrine\Repository\PlayerRepository;
use App\Shared\Domain\Bus\Command\CommandHandler;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus', handles: UpdatePlayer::class)]
readonly class UpdatePlayerHandler implements CommandHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private PlayerRepository $playerRepository,
    ) {
    }

    public function __invoke(UpdatePlayer $updatePlayer): void
    {
        $playerRequestedEvent = new PlayerRequestedEvent($updatePlayer->id);

        $this->eventDispatcher->dispatch($playerRequestedEvent);

        $player = $this->playerRepository->find($updatePlayer->id);

        $player->update($updatePlayer->name);

        $playerUpdatedEvent = new PlayerUpdatedEvent($player);

        $this->eventDispatcher->dispatch($playerUpdatedEvent);
    }
}
