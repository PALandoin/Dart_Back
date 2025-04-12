<?php

declare(strict_types=1);

namespace App\Tests\Functional\Game\Infrastructure\Controller;

use App\Game\Infrastructure\Doctrine\Factory\GameFactory;
use App\Player\Infrastructure\Doctrine\Factory\PlayerFactory;
use App\Tests\ProviderClass;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Json;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ReadGameControllerTest extends KernelTestCase
{
    use Factories;
    use HasBrowser;
    use ResetDatabase;

    public static function readGameProvider(): Generator
    {
        // Case 1: Valid game ID
        yield 'successful_game_read' => [
            new ProviderClass(
                function () {
                    $players = PlayerFactory::createMany(2);
                    $game = GameFactory::createOne([
                        'players' => $players,
                    ]);

                    return [
                        'gameId' => $game->getId(),
                        'game' => $game,
                    ];
                },
                200,
                function (Json $response): void {
                    $game = GameFactory::repository()->first();

                    $response->assertHas('message')
                        ->assertHas('data')
                        ->assertHas('data.id')
                        ->assertHas('data.status')
                        ->assertHas('data.players')
                        ->assertThat('message', fn (Json $message) => $message->equals('Game successfully read'))
                        ->assertThat('data.id', fn (Json $id) => $id->equals($game->getId()))
                        ->assertThat('data.status', fn (Json $status) => $status->equals($game->getStatus()->value))
                        ->assertThat('data.players', fn (Json $players) => $players->hasCount(2));
                },
            ),
        ];

        // Case 2: Invalid game ID
        yield 'game_not_found' => [
            new ProviderClass(
                fn () => [
                    'gameId' => 999,
                ],
                404,
                function (Json $response): void {
                    $response->assertHas('message')
                        ->assertThat(
                            'message',
                            fn (Json $message) => $message->equals('Handling "App\Game\Application\Query\ReadGame\ReadGame" failed: Game with id 999 not found'),
                        );
                },
            ),
        ];
    }

    #[DataProvider('readGameProvider')]
    public function testReadGame(ProviderClass $testCase): void
    {
        $requestData = $testCase->getRequestData();
        $gameId = $requestData['gameId'];

        $response = $this->browser()
            ->get("api/games/{$gameId}")
            ->assertStatus($testCase->getExpectedStatus())
            ->json();

        $testCase->assertResponse($response);
    }
}
