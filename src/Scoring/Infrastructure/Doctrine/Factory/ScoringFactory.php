<?php

declare(strict_types=1);

namespace App\Scoring\Infrastructure\Doctrine\Factory;

use App\Game\Infrastructure\Doctrine\Factory\GameFactory;
use App\Player\Infrastructure\Doctrine\Factory\PlayerFactory;
use App\Scoring\Domain\Model\Scoring;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Scoring>
 */
final class ScoringFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Scoring::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'game' => GameFactory::createOne(),
            'player' => PlayerFactory::createOne(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Scoring $scoring): void {})
        ;
    }
}
