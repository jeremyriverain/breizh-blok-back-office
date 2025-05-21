<?php

namespace App\DataFixtures\Factory;

use App\Entity\GeoPoint;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<GeoPoint>
 */
final class GeoPointFactory extends PersistentProxyObjectFactory
{

    public static function class(): string
    {
        return GeoPoint::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        return [
            'latitude' => self::faker()->randomFloat(min: -90, max: 90),
            'longitude' => self::faker()->randomFloat(min: -180, max: 180),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}
