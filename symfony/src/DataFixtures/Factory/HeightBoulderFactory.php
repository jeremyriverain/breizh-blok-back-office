<?php

namespace App\DataFixtures\Factory;

use App\Entity\HeightBoulder;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<HeightBoulder>
 */
final class HeightBoulderFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return HeightBoulder::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'min' => self::faker()->numberBetween(1, 32767),
        ];
    }
}
