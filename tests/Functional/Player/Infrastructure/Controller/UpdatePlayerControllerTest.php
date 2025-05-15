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

class UpdatePlayerControllerTest extends KernelTestCase
{
    use Factories;
    use HasBrowser;
    use ResetDatabase;

    public static function updatePlayerProvider(): Generator
    {
        // Case 1: Valid player update
        yield 'successful_player_update' => [
            new ProviderClass(
                function () {
                    $player = PlayerFactory::createOne(['name' => 'Florian']);

                    return [
                        'playerId' => $player->getId(),
                        'request' => [
                            'json' => [
                                'name' => 'Pierre-Arnaud',
                            ],
                        ],
                    ];
                },
                200,
                function (Json $response): void {
                    $response->assertHas('message')
                        ->assertHas('data')
                        ->assertHas('data.id')
                        ->assertHas('data.name')
                        ->assertThat('message', fn(Json $message) => $message->equals('Player successfully updated'))
                        ->assertThat('data.name', fn(Json $name) => $name->equals('Pierre-Arnaud'));

                    $players = PlayerFactory::all();
                    self::assertCount(1, $players);
                },
            ),
        ];

        // Case 2: Empty name validation error
        yield 'empty_name_validation_error' => [
            new ProviderClass(
                function () {
                    $player = PlayerFactory::createOne(['name' => 'Florian']);

                    return [
                        'playerId' => $player->getId(),
                        'request' => [
                            'json' => [
                                'name' => '',
                            ],
                        ],
                    ];
                },
                422,
                function (Json $response): void {
                    $response->assertHas('message')
                        ->assertHas('data')
                        ->assertHas('data.name')
                        ->assertThat('message', fn(Json $message) => $message->equals('Validation error'))
                        ->assertThat('data.name', fn(Json $name) => $name->equals('This value should not be blank.'));
                },
            ),
        ];

        // Case 3: No name in body validation error
        yield 'no_name_in_body_validation_error' => [
            new ProviderClass(
                function () {
                    $player = PlayerFactory::createOne(['name' => 'Florian']);

                    return [
                        'playerId' => $player->getId(),
                        'request' => [
                            'json' => [],
                        ],
                    ];
                },
                422,
                function (Json $response): void {
                    $response->assertHas('message')
                        ->assertHas('data')
                        ->assertHas('data.name')
                        ->assertThat('message', fn(Json $message) => $message->equals('Validation error'))
                        ->assertThat('data.name', fn(Json $name) => $name->equals('This value should not be blank.'));
                },
            ),
        ];

        // Case 4: Null name validation error
        yield 'null_name_validation_error' => [
            new ProviderClass(
                function () {
                    $player = PlayerFactory::createOne(['name' => 'Florian']);

                    return [
                        'playerId' => $player->getId(),
                        'request' => [
                            'json' => [
                                'name' => null,
                            ],
                        ],
                    ];
                },
                422,
                function (Json $response): void {
                    $response->assertHas('message')
                        ->assertHas('data')
                        ->assertHas('data.name')
                        ->assertThat('message', fn(Json $message) => $message->equals('Validation error'))
                        ->assertThat('data.name', fn(Json $name) => $name->equals('This value should be of type string.'));
                },
            ),
        ];

        // Case 5: Already existing name validation error
        yield 'duplicate_name_validation_error' => [
            new ProviderClass(
                function () {
                    PlayerFactory::createOne(['name' => 'Florian de test']);
                    $player = PlayerFactory::createOne(['name' => 'Florian']);

                    return [
                        'playerId' => $player->getId(),
                        'request' => [
                            'json' => [
                                'name' => 'Florian de test',
                            ],
                        ],
                    ];
                },
                422,
                function (Json $response): void {
                    $response->assertHas('message')
                        ->assertThat('message', fn(Json $message) => $message->contains('This value is already used'));
                },
            ),
        ];

        // Case 6: Player not found error
        yield 'player_not_found_error' => [
            new ProviderClass(
                fn() => [
                    'playerId' => 1,
                    'request' => [
                        'json' => [
                            'name' => 'Florian',
                        ],
                    ],
                ],
                404,
                function (Json $response): void {
                    $response->assertHas('message')
                        ->assertThat(
                            'message',
                            fn(Json $message) => $message->equals('Handling "App\Player\Application\Command\UpdatePlayer\UpdatePlayer" failed: Player with id 1 not found'),
                        );
                },
            ),
        ];
    }

    #[DataProvider('updatePlayerProvider')]
    public function testUpdatePlayer(ProviderClass $testCase): void
    {
        $requestData = $testCase->getRequestData();
        $playerId = $requestData['playerId'];
        $request = $requestData['request'] ?? [];

        $response = $this->browser()
            ->patch("api/players/{$playerId}", $request)
            ->assertStatus($testCase->getExpectedStatus())
            ->json();

        $testCase->assertResponse($response);
    }
}
