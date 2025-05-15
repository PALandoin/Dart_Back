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

class CreatePlayerControllerTest extends KernelTestCase
{
    use Factories;
    use HasBrowser;
    use ResetDatabase;

    public static function createPlayerProvider(): Generator
    {
        // Case 1: Valid player creation
        yield 'successful_player_creation' => [
            new ProviderClass(
                fn() => [
                    'request' => [
                        'json' => [
                            'name' => 'Florian',
                        ],
                    ],
                ],
                201,
                function (Json $response): void {
                    $response->assertHas('message')
                        ->assertHas('data')
                        ->assertHas('data.id')
                        ->assertHas('data.name')
                        ->assertThat('message', fn(Json $message) => $message->equals('Player successfully created'))
                        ->assertThat('data.name', fn(Json $name) => $name->equals('Florian'));

                    $players = PlayerFactory::all();
                    self::assertCount(1, $players);
                },
            ),
        ];

        // Case 2: Null name validation error
        yield 'null_name_validation_error' => [
            new ProviderClass(
                fn() => [
                    'request' => [
                        'json' => [
                            'name' => null,
                        ],
                    ],
                ],
                422,
                function (Json $response): void {
                    $response->assertHas('message')
                        ->assertHas('data')
                        ->assertHas('data.name')
                        ->assertThat('message', fn(Json $message) => $message->equals('Validation error'))
                        ->assertThat('data.name', fn(Json $name) => $name->equals('This value should be of type string.'));

                    $players = PlayerFactory::all();
                    self::assertCount(0, $players);
                },
            ),
        ];

        // Case 3: Empty name validation error
        yield 'empty_name_validation_error' => [
            new ProviderClass(
                fn() => [
                    'request' => [
                        'json' => [
                            'name' => '',
                        ],
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Language' => 'fr',
                        ],
                    ],
                ],
                422,
                function (Json $response): void {
                    $response->assertHas('message')
                        ->assertHas('data')
                        ->assertHas('data.name')
                        ->assertThat('message', fn(Json $message) => $message->equals('Validation error'))
                        ->assertThat('data.name', fn(Json $name) => $name->equals('This value should not be blank.'));

                    $players = PlayerFactory::all();
                    self::assertCount(0, $players);
                },
            ),
        ];

        // Case 4: Duplicate name validation error
        yield 'duplicate_name_validation_error' => [
            new ProviderClass(
                function () {
                    PlayerFactory::createOne(['name' => 'Florian']);

                    return [
                        'request' => [
                            'json' => [
                                'name' => 'Florian',
                            ],
                            'headers' => [
                                'Content-Type' => 'application/json',
                                'Language' => 'fr',
                            ],
                        ],
                    ];
                },
                422,
                function (Json $response): void {
                    $response->assertHas('message')
                        ->assertThat('message', fn(Json $message) => $message->contains('This value is already used'));

                    $players = PlayerFactory::all();
                    self::assertCount(1, $players);
                },
            ),
        ];
    }

    #[DataProvider('createPlayerProvider')]
    public function testCreatePlayer(ProviderClass $testCase): void
    {
        $requestData = $testCase->getRequestData();

        $response = $this->browser()
            ->post('api/players', $requestData['request'])
            ->assertStatus($testCase->getExpectedStatus())
            ->json();

        $testCase->assertResponse($response);
    }
}
