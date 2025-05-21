<?php

namespace App\DataFixtures\Factory;

use App\Entity\LineBoulder;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<LineBoulder>
 */
final class LineBoulderFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return LineBoulder::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'arrArrPoints' => [],
            'boulder' => BoulderFactory::new(),
            'rockImage' => MediaFactory::new(),
            'smoothLine' => self::faker()->text(),
        ];
    }
}
