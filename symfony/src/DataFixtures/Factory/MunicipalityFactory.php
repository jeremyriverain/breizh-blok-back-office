<?php

namespace App\DataFixtures\Factory;

use App\Entity\Municipality;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Municipality>
 */
final class MunicipalityFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Municipality::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'name' => self::faker()->text(150),
        ];
    }
}
