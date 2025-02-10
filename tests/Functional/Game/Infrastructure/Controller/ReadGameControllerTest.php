<?php

namespace App\Tests\Functional\Game\Infrastructure\Controller;

use App\Game\Infrastructure\Doctrine\Factory\GameFactory;
use App\Player\Infrastructure\Doctrine\Factory\PlayerFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Json;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class ReadGameControllerTest extends KernelTestCase
{
    use Factories;
    use ResetDatabase;
    use HasBrowser;

    public function testCanReadGame(): void
    {
        $players = PlayerFactory::createMany(2);

        $game = GameFactory::createOne([
            'players' => $players,
        ]);

        $response = $this->browser()
            ->get("api/games/{$game->getId()}")
            ->assertSuccessful()
            ->json();

        $response->assertHas('message')
            ->assertHas('data')
            ->assertHas('data.id')
            ->assertHas('data.status')
            ->assertHas('data.players')
            ->assertThat('message', fn (Json $message) => $message->equals('Game successfully read'))
            ->assertThat('data.id', fn (Json $id) => $id->equals($game->getId()))
            ->assertThat('data.status', fn (Json $status) => $status->equals($game->getStatus()->value))
            ->assertThat('data.players', fn (Json $players) => $players->hasCount(2));
    }

    public function testCannotReadGameWithInvalidId(): void
    {
        $response = $this->browser()
            ->get('api/games/999')
            ->assertStatus(404)
            ->json();

        $response->assertHas('message')
            ->assertThat('message', fn ($message) => $message->equals('Handling "App\Game\Application\Query\ReadGame\ReadGame" failed: Game with id 999 not found'));
    }
}
