<?php

namespace App\Scoring\Application\Command\CreateScoring;

use App\Game\Domain\Event\GameRequestedEvent;
use App\Game\Domain\Model\Game;
use App\Game\Domain\Repository\GameRepository;
use App\Player\Domain\Event\PlayerRequestedEvent;
use App\Player\Domain\Model\Player;
use App\Player\Domain\Repository\PlayerRepository;
use App\Scoring\Domain\Event\ScoringCreatedEvent;
use App\Scoring\Domain\Model\DartThrow;
use App\Scoring\Domain\Model\Scoring;
use App\Shared\Domain\Bus\Command\CommandHandler;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus', handles: CreateScoring::class)]
readonly class CreateScoringHandler implements CommandHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private PlayerRepository $playerRepository,
        private GameRepository $gameRepository,
    ) {
    }

    public function __invoke(CreateScoring $createScoring): void
    {
        $playerRequest = new PlayerRequestedEvent($createScoring->playerId);

        $this->eventDispatcher->dispatch($playerRequest);

        /** @var Player $player */
        $player = $this->playerRepository->find($createScoring->playerId);

        $gameRequest = new GameRequestedEvent($createScoring->gameId, $createScoring->playerId);

        $this->eventDispatcher->dispatch($gameRequest);

        /** @var Game $game */
        $game = $this->gameRepository->find($createScoring->gameId);

        $scoring = Scoring::create($game, $player);

        $dartThrow = DartThrow::create($createScoring->dartThrow->multiplication, $createScoring->dartThrow->section, $scoring);

        $scoring->addDartThrow($dartThrow);

        $scoringCreatedEvent = new ScoringCreatedEvent($scoring);

        $this->eventDispatcher->dispatch($scoringCreatedEvent);
    }
}
