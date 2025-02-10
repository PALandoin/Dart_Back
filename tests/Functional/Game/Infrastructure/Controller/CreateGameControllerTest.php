<?php

namespace App\Tests\Functional\Game\Infrastructure\Controller;

use App\Game\Domain\Model\Enum\GameStatus;
use App\Player\Infrastructure\Doctrine\Factory\PlayerFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Json;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CreateGameControllerTest extends KernelTestCase
{
    use Factories;
    use HasBrowser;
    use ResetDatabase;

    public function testGameBeCreated(): void
    {
        $players = PlayerFactory::createMany(2);
        $response = $this->browser()->post('/api/games',
            [
                'json' => [
                    'players' => [
                        $players[0]->getId(),
                        $players[1]->getId(),
                    ],
                ],
            ]
        )
            ->assertStatus(201)
            ->json();

        $response->assertHas('message')
            ->assertHas('data')
            ->assertHas('data.id')
            ->assertHas('data.status')
            ->assertThat('data.status', fn (Json $status) => $status->equals(GameStatus::CREATED->value))
            ->assertHas('data.players')
            ->assertThat('data.players', fn (Json $players) => $players->hasCount(2))
            ->assertHas('data.players[0].id')
            ->assertThat('data.players[0].id', fn (Json $id) => $id->equals($players[0]->getId()))
            ->assertHas('data.players[0].name')
            ->assertThat('data.players[0].name', fn (Json $name) => $name->equals($players[0]->getName()))
            ->assertHas('data.players[1].id')
            ->assertThat('data.players[1].id', fn (Json $id) => $id->equals($players[1]->getId()))
            ->assertHas('data.players[1].name')
            ->assertThat('data.players[1].name', fn (Json $name) => $name->equals($players[1]->getName()));
    }

    public function testGameBeCreatedWithInvalidPlayers(): void
    {
        $response = $this->browser()->post('/api/games',
            [
                'json' => [
                    'players' => [1, 2],
                ],
            ]
        )
            ->assertStatus(404)
            ->json();

        $response->assertHas('message')
            ->assertThat('message', fn (Json $message) => $message->equals('Handling "App\Game\Application\Command\CreateGame\CreateGame" failed: Player with id 1 not found'));
    }
}
