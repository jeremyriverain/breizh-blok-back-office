<?php

namespace App\DataFixtures\Factory;

use App\Entity\BoulderArea;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<BoulderArea>
 */
final class BoulderAreaFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return BoulderArea::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'createdAt' => self::faker()->dateTime(),
            'municipality' => MunicipalityFactory::new(),
            'name' => self::faker()->text(255),
        ];
    }
}
