<?php

namespace App\DataFixtures\Factory;

use App\Entity\Boulder;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Boulder>
 */
final class BoulderFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Boulder::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'createdAt' => self::faker()->dateTime(),
            'name' => self::faker()->text(255),
        ];
    }
}
