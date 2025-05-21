<?php

namespace App\DataFixtures\Factory;

use App\Entity\Grade;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Grade>
 */
final class GradeFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Grade::class;
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array|callable
    {
        return [
            'name' => '6a',
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}
