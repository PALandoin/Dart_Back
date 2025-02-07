<?php

namespace App\Player\Application\Command\CreatePlayer;

use App\Player\Domain\Event\PlayerCreatedEvent;
use App\Player\Domain\Model\Player;
use App\Shared\Domain\Bus\Command\CommandHandler;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus', handles: CreatePlayer::class)]
readonly class CreatePlayerHandler implements CommandHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function __invoke(CreatePlayer $createPlayer): void
    {
        $player = Player::register($createPlayer->name);

        $this->eventDispatcher->dispatch(new PlayerCreatedEvent($player));
    }
}
