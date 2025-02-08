<?php

namespace App\Player\Application\Query\ReadPlayer;

use App\Player\Application\Query\PlayerResponse;
use App\Player\Domain\Event\PlayerRequestedEvent;
use App\Player\Domain\Model\Player;
use App\Player\Domain\Repository\PlayerRepository;
use App\Shared\Domain\Bus\Query\QueryHandler;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus', handles: ReadPlayer::class)]
readonly class ReadPlayerHandler implements QueryHandler
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(ReadPlayer $readPlayer): PlayerResponse
    {
        $playerRequestedEvent = new PlayerRequestedEvent($readPlayer->id);

        $this->eventDispatcher->dispatch($playerRequestedEvent);

        /** @var Player $player */
        $player = $this->playerRepository->find($readPlayer->id);

        return PlayerResponse::fromModel($player);
    }
}
