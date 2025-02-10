<?php

declare(strict_types=1);

namespace App\Tests\Functional\Player\Infrastructure\Controller;

use App\Player\Infrastructure\Doctrine\Factory\PlayerFactory;
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

    public function testPlayerCanBeCreated(): void
    {
        $response = $this->browser()
            ->post('api/players',
                [
                    'json' => [
                        'name' => 'Florian le gros bébé',
                    ],
                ]
            )
            ->assertStatus(201)
            ->json();

        $response->assertHas('message')
            ->assertHas('data')
            ->assertHas('data.id')
            ->assertHas('data.name')
            ->assertThat('message', fn (Json $message) => $message->equals('Player successfully created'))
            ->assertThat('data.name', fn (Json $name) => $name->equals('Florian le gros bébé'));

        $players = PlayerFactory::all();

        $this->assertCount(1, $players);
    }

    public function testPlayerCannotBeCreatedWithNullName(): void
    {
        $response = $this->browser()
            ->post('api/players',
                [
                    'json' => [
                        'name' => null,
                    ],
                ]
            )
            ->assertStatus(422)
            ->json();

        $response->assertHas('message')
            ->assertHas('data')
            ->assertHas('data.name')
            ->assertThat('message', fn (Json $message) => $message->equals('Validation error'))
            ->assertThat('data.name', fn (Json $name) => $name->equals('This value should be of type string.'));

        $players = PlayerFactory::all();

        $this->assertCount(0, $players);
    }

    public function testPlayerCannotBeCreatedWithEmptyName(): void
    {
        $response = $this->browser()
            ->post('api/players',
                [
                    'json' => [
                        'name' => '',
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Language' => 'fr',
                    ],
                ]
            )
            ->assertStatus(422)
            ->json();

        $response->assertHas('message')
            ->assertHas('data')
            ->assertHas('data.name')
            ->assertThat('message', fn (Json $message) => $message->equals('Validation error'))
            ->assertThat('data.name', fn (Json $name) => $name->equals('This value should not be blank.'));

        $players = PlayerFactory::all();

        $this->assertCount(0, $players);
    }

    public function testPlayerCannotBeCreatedWithAlreadyExistingName(): void
    {
        PlayerFactory::createOne(['name' => 'Florian le gros bébé']);

        $response = $this->browser()
            ->post('api/players',
                [
                    'json' => [
                        'name' => 'Florian le gros bébé',
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Language' => 'fr',
                    ],
                ]
            )
            ->assertStatus(422)
            ->json();

        $response->assertHas('message')
            ->assertThat('message', fn (Json $message) => $message->contains('This value is already used'));

        $players = PlayerFactory::all();

        $this->assertCount(1, $players);
    }
}
