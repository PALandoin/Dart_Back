<?php

namespace App\Tests\Functional\Player\Controller;

use App\Player\Infrastructure\Doctrine\Factory\PlayerFactory;
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

    public function testPlayerCanBeRead(): void
    {
        $player = PlayerFactory::createOne(['name' => 'Florian']);

        $response = $this->browser()
            ->request('GET', "api/players/{$player->getId()}")
            ->assertStatus(200)
            ->json();

        $response->assertHas('message')
            ->assertHas('data')
            ->assertHas('data.id')
            ->assertHas('data.name')
            ->assertThat('message', fn (Json $message) => $message->equals('Player successfully read'))
            ->assertThat('data.name', fn (Json $name) => $name->equals('Florian'));
    }

    public function testPlayerCannotBeReadWithInvalidId(): void
    {
        $response = $this->browser()
            ->request('GET', 'api/players/999')
            ->assertStatus(404)
            ->json();

        $response->assertHas('message')
            ->assertThat('message', fn (Json $message) => $message->equals('Handling "App\Player\Application\Query\ReadPlayer\ReadPlayer" failed: Player not found'));
    }
}
