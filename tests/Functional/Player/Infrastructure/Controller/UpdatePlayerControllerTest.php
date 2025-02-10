<?php

namespace App\Tests\Functional\Player\Infrastructure\Controller;

use App\Player\Infrastructure\Doctrine\Factory\PlayerFactory;
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

    public function testPlayerCanBeUpdated(): void
    {
        $player = PlayerFactory::createOne(['name' => 'Florian']);

        $response = $this->browser()
            ->patch("api/players/{$player->getId()}",
                [
                    'json' => [
                        'name' => 'Florian le gros bébé',
                    ],
                ]
            )
            ->assertStatus(200)
            ->json();

        $response->assertHas('message')
            ->assertHas('data')
            ->assertHas('data.id')
            ->assertHas('data.name')
            ->assertThat('message', fn (Json $message) => $message->equals('Player successfully updated'))
            ->assertThat('data.name', fn (Json $name) => $name->equals('Florian le gros bébé'));

        $players = PlayerFactory::all();

        $this->assertCount(1, $players);
    }

    public function testPlayerCannotBeUpdatedWithEmptyName(): void
    {
        $player = PlayerFactory::createOne(['name' => 'Florian']);

        $response = $this->browser()
            ->patch("api/players/{$player->getId()}",
                [
                    'json' => [
                        'name' => '',
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
    }

    public function testPlayerCannotBeUpdatedWithNoNameInBody(): void
    {
        $player = PlayerFactory::createOne(['name' => 'Florian']);

        $response = $this->browser()
            ->patch("api/players/{$player->getId()}",
                [
                    'json' => [
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
    }

    public function testPlayerCannotBeUpdatedWithNullName(): void
    {
        $player = PlayerFactory::createOne(['name' => 'Florian']);

        $response = $this->browser()
            ->patch("api/players/{$player->getId()}",
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
    }

    public function testPlayerCannotBeUpdatedWithAlreadyExistingName(): void
    {
        PlayerFactory::createOne(['name' => 'Florian le gros bébé']);
        $player = PlayerFactory::createOne(['name' => 'Florian']);

        $response = $this->browser()
            ->patch("api/players/{$player->getId()}",
                [
                    'json' => [
                        'name' => 'Florian le gros bébé',
                    ],
                ]
            )
            ->assertStatus(422)
            ->json();

        $response->assertHas('message')
            ->assertThat('message', fn (Json $message) => $message->contains('This value is already used'));
    }

    public function testPlayerCannotBeUpdatedWithNoPlayer(): void
    {
        $response = $this->browser()
            ->patch('api/players/1',
                [
                    'json' => [
                        'name' => 'Florian le gros bébé',
                    ],
                ]
            )
            ->assertStatus(404)
            ->json();

        $response->assertHas('message')
            ->assertThat('message', fn (Json $message) => $message->equals('Handling "App\Player\Application\Command\UpdatePlayer\UpdatePlayer" failed: Player with id 1 not found'));
    }
}
