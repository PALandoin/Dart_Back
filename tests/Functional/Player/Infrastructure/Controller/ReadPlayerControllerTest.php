<?php

declare(strict_types=1);

namespace App\Tests\Functional\Player\Infrastructure\Controller;

use App\Player\Infrastructure\Doctrine\Factory\PlayerFactory;
use App\Tests\ProviderClass;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Json;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ReadPlayerControllerTest extends KernelTestCase
{
    use Factories;
    use HasBrowser;
    use ResetDatabase;

    public static function readPlayerProvider(): Generator
    {
        // Case 1: Valid player read
        yield 'successful_player_read' => [
            new ProviderClass(
                function () {
                    $player = PlayerFactory::createOne(['name' => 'Florian']);

                    return [
                        'playerId' => $player->getId(),
                    ];
                },
                200,
                function (Json $response): void {
                    $player = PlayerFactory::repository()->first();

                    $response->assertHas('message')
                        ->assertHas('data')
                        ->assertHas('data.id')
                        ->assertHas('data.name')
                        ->assertThat('message', fn (Json $message) => $message->equals('Player successfully read'))
                        ->assertThat('data.name', fn (Json $name) => $name->equals('Florian'));
                },
            ),
        ];

        // Case 2: Invalid player ID
        yield 'player_not_found' => [
            new ProviderClass(
                fn () => [
                    'playerId' => 999,
                ],
                404,
                function (Json $response): void {
                    $response->assertHas('message')
                        ->assertThat(
                            'message',
                            fn (Json $message) => $message->equals('Handling "App\Player\Application\Query\ReadPlayer\ReadPlayer" failed: Player with id 999 not found'),
                        );
                },
            ),
        ];
    }

    #[DataProvider('readPlayerProvider')]
    public function testReadPlayer(ProviderClass $testCase): void
    {
        $requestData = $testCase->getRequestData();
        $playerId = $requestData['playerId'];

        $response = $this->browser()
            ->get("api/players/{$playerId}")
            ->assertStatus($testCase->getExpectedStatus())
            ->json();

        $testCase->assertResponse($response);
    }
}
