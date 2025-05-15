<?php

declare(strict_types=1);

namespace App\Scoring\Infrastructure\Doctrine\Factory;

use App\Scoring\Domain\Model\DartThrow;
use App\Scoring\Domain\Model\Enum\MultiplicationEnum;
use App\Scoring\Domain\Model\Enum\SectionEnum;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<DartThrow>
 */
final class DartThrowFactory extends PersistentProxyObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return DartThrow::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'multiplication' => self::faker()->randomElement(MultiplicationEnum::cases()),
            'scoring' => ScoringFactory::createOne(),
            'section' => self::faker()->randomElement(SectionEnum::cases()),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(DartThrow $dartThrow): void {})
        ;
    }
}
