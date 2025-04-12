<?php

declare(strict_types=1);

namespace App\Tests\Functional\Scoring\Infrastructure\Controller;

use App\Game\Domain\Model\Enum\GameStatus;
use App\Game\Infrastructure\Doctrine\Factory\GameFactory;
use App\Player\Infrastructure\Doctrine\Factory\PlayerFactory;
use App\Scoring\Infrastructure\Doctrine\Factory\DartThrowFactory;
use App\Scoring\Infrastructure\Doctrine\Factory\ScoringFactory;

use function sprintf;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Json;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CreateScoringControllerTest extends KernelTestCase
{
    use Factories;
    use HasBrowser;
    use ResetDatabase;

    public function testCanCreateScoringOnCreatedGame(): void
    {
        $player1 = PlayerFactory::createOne();
        $player2 = PlayerFactory::createOne();
        $game = GameFactory::createOne([
            'players' => [$player1, $player2],
            'status' => GameStatus::CREATED,
        ]);

        $response = $this->browser()
            ->post('api/scorings', [
                'json' => [
                    'gameId' => $game->getId(),
                    'playerId' => $player1->getId(),
                    'dartThrow' => [
                        'multiplication' => 1,
                        'section' => 20,
                    ],
                ],
            ])
            ->assertSuccessful()
            ->json();

        $response->assertHas('message')
            ->assertHas('data')
            ->assertHas('data.id')
            ->assertHas('data.game')
            ->assertHas('data.player')
            ->assertThat('message', fn (Json $message) => $message->equals('Scoring successfully created'))
            ->assertThat('data.game.id', fn (Json $gameId) => $gameId->equals($game->getId()))
            ->assertThat('data.game.status', fn (Json $gameStatus) => $gameStatus->equals(GameStatus::STARTED->value))
            ->assertThat('data.game.scores."'.$player1->getId().'"', fn (Json $player1Score) => $player1Score->equals(20))
            ->assertThat('data.game.scores."'.$player2->getId().'"', fn (Json $player2Score) => $player2Score->equals(0))
            ->assertThat('data.player.id', fn (Json $playerId) => $playerId->equals($player1->getId()));
    }

    public function testCanCreateScoringOnStartedGame(): void
    {
        $player1 = PlayerFactory::createOne();
        $player2 = PlayerFactory::createOne();
        $game = GameFactory::createOne([
            'players' => [$player1, $player2],
            'status' => GameStatus::STARTED,
        ]);
        $scoringPlayer1 = ScoringFactory::createOne([
            'player' => $player1,
            'game' => $game,
        ]);
        $dartThrowsScoringPlayer1 = DartThrowFactory::createMany(3, [
            'scoring' => $scoringPlayer1,
        ]);
        foreach ($dartThrowsScoringPlayer1 as $dartThrow) {
            $scoringPlayer1->addDartThrow($dartThrow->_real());
        }
        $scoringPlayer2 = ScoringFactory::createOne([
            'player' => $player2,
            'dartThrows' => DartThrowFactory::createMany(3),
            'game' => $game,
        ]);
        $dartThrowsScoringPlayer2 = DartThrowFactory::createMany(3, [
            'scoring' => $scoringPlayer2,
        ]);
        foreach ($dartThrowsScoringPlayer2 as $dartThrow) {
            $scoringPlayer2->addDartThrow($dartThrow->_real());
        }

        $response = $this->browser()
            ->post('api/scorings', [
                'json' => [
                    'gameId' => $game->getId(),
                    'playerId' => $player1->getId(),
                    'dartThrow' => [
                        'multiplication' => 1,
                        'section' => 20,
                    ],
                ],
            ])
            ->assertSuccessful()
            ->json();

        $response->assertHas('message')
            ->assertHas('data')
            ->assertHas('data.id')
            ->assertHas('data.game')
            ->assertHas('data.player')
            ->assertHas('data.score')
            ->assertThat('message', fn (Json $message) => $message->equals('Scoring successfully created'))
            ->assertThat('data.game.id', fn (Json $gameId) => $gameId->equals($game->getId()))
            ->assertThat('data.game.status', fn (Json $gameStatus) => $gameStatus->equals(GameStatus::STARTED->value))
            ->assertHas('data.game.scores')
            ->assertThat('data.game.scores', fn (Json $scores) => $scores->hasCount(2))
            ->assertThat('data.player.id', fn (Json $playerId) => $playerId->equals($player1->getId()))
            ->assertThat('data.score', fn (Json $score) => $score->equals(20));

        $expectedScores = [
            $player1->getId() => 20 + $scoringPlayer1->getScore(),
            $player2->getId() => $scoringPlayer2->getScore(),
        ];

        foreach ($game->getPlayers() as $player) {
            $response->assertThat(
                sprintf('data.game.scores."%d"', $player->getId()),
                fn (Json $score) => $score->equals($expectedScores[$player->getId()]),
            );
        }
    }

    public function testCannotCreateScoringWithGameNotFound(): void
    {
        $player = PlayerFactory::createOne();

        $response = $this->browser()
            ->post('api/scorings', [
                'json' => [
                    'gameId' => 1,
                    'playerId' => $player->getId(),
                    'dartThrow' => [
                        'multiplication' => 1,
                        'section' => 20,
                    ],
                ],
            ])
            ->assertStatus(404)
            ->json();

        $response->assertHas('message')
            ->assertThat('message', fn (Json $message) => $message->equals('Handling "App\Scoring\Application\Command\CreateScoring\CreateScoring" failed: Game with id 1 not found'));
    }

    public function testCannotCreateScoringWithPlayerNotFound(): void
    {
        $game = GameFactory::createOne();

        $response = $this->browser()
            ->post('api/scorings', [
                'json' => [
                    'gameId' => $game->getId(),
                    'playerId' => 1,
                    'dartThrow' => [
                        'multiplication' => 1,
                        'section' => 20,
                    ],
                ],
            ])
            ->assertStatus(404)
            ->json();

        $response->assertHas('message')
            ->assertThat('message', fn (Json $message) => $message->equals('Handling "App\Scoring\Application\Command\CreateScoring\CreateScoring" failed: Player with id 1 not found'));
    }

    public function testCannotCreateScoringWithPlayerNotInGame(): void
    {
        $player = PlayerFactory::createOne();
        $game = GameFactory::createOne([
            'players' => [],
        ]);

        $response = $this->browser()
            ->post('api/scorings', [
                'json' => [
                    'gameId' => $game->getId(),
                    'playerId' => $player->getId(),
                    'dartThrow' => [
                        'multiplication' => 1,
                        'section' => 20,
                    ],
                ],
            ])
            ->assertStatus(422)
            ->json();

        $response->assertHas('message')
            ->assertThat('message', fn (Json $message) => $message->equals('Handling "App\Scoring\Application\Command\CreateScoring\CreateScoring" failed: Game with id '.$game->getId().' not found for player with id '.$player->getId()));
    }

    public function testCannotCreateScoringWithInvalidMultiplication(): void
    {
        $player = PlayerFactory::createOne();
        $game = GameFactory::createOne([
            'players' => [$player],
        ]);

        $response = $this->browser()
            ->post('api/scorings', [
                'json' => [
                    'gameId' => $game->getId(),
                    'playerId' => $player->getId(),
                    'dartThrow' => [
                        'multiplication' => 4,
                        'section' => 20,
                    ],
                ],
            ])
            ->assertStatus(422)
            ->json();

        $response->assertHas('message')
            ->assertHas('data')
            ->assertThat('message', fn (Json $message) => $message->equals('Validation error'))
            ->assertThat('data."dartThrow.multiplication"', fn (Json $message) => $message->equals('The value you selected is not a valid choice.'));
    }

    public function testCannotCreateScoringWithInvalidSection(): void
    {
        $player = PlayerFactory::createOne();
        $game = GameFactory::createOne([
            'players' => [$player],
        ]);

        $response = $this->browser()
            ->post('api/scorings', [
                'json' => [
                    'gameId' => $game->getId(),
                    'playerId' => $player->getId(),
                    'dartThrow' => [
                        'multiplication' => 1,
                        'section' => 21,
                    ],
                ],
            ])
            ->assertStatus(422)
            ->json();

        $response->assertHas('message')
            ->assertHas('data')
            ->assertThat('message', fn (Json $message) => $message->equals('Validation error'))
            ->assertThat('data."dartThrow.section"', fn (Json $message) => $message->equals('The value you selected is not a valid choice.'));
    }

    public function testCannotCreateScoringWithInvalidGameStatus(): void
    {
        $player = PlayerFactory::createOne();
        $game = GameFactory::createOne([
            'status' => GameStatus::FINISHED,
            'players' => [$player],
        ]);

        $response = $this->browser()
            ->post('api/scorings', [
                'json' => [
                    'gameId' => $game->getId(),
                    'playerId' => $player->getId(),
                    'dartThrow' => [
                        'multiplication' => 1,
                        'section' => 20,
                    ],
                ],
            ])
            ->assertStatus(422)
            ->json();

        $response->assertHas('message')
            ->assertThat('message', fn (Json $message) => $message->equals('Handling "App\Scoring\Application\Command\CreateScoring\CreateScoring" failed: Game is finished'));
    }

    public function testCannotCreateScoringWithEmptyThrow(): void
    {
        $player = PlayerFactory::createOne();
        $game = GameFactory::createOne([
            'players' => [$player],
        ]);

        $response = $this->browser()
            ->post('api/scorings', [
                'json' => [
                    'gameId' => $game->getId(),
                    'playerId' => $player->getId(),
                ],
            ])
            ->assertStatus(422)
            ->json();

        $response->assertHas('message')
            ->assertHas('data')
            ->assertThat('message', fn (Json $message) => $message->equals('Validation error'))
            ->assertThat('data."dartThrow"', fn (Json $message) => $message->equals('This value should be of type App\Scoring\Application\Command\Subresource\CreateDartThrow.'));
    }
}
