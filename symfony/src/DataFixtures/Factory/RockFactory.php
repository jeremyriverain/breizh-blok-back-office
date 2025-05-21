<?php

namespace App\DataFixtures\Factory;

use App\Entity\Rock;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Rock>
 */
final class RockFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Rock::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'boulderArea' => BoulderAreaFactory::new(),
            'createdAt' => self::faker()->dateTime(),
            'location' => GeoPointFactory::new(),
        ];
    }
}
