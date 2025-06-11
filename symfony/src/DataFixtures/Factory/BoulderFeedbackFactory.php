<?php

namespace App\DataFixtures\Factory;

use App\Entity\BoulderFeedback;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<BoulderFeedback>
 */
final class BoulderFeedbackFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return BoulderFeedback::class;
    }

    /**
     * @return array<string, mixed>
    */
    protected function defaults(): array
    {
        return [
            'boulder' => BoulderFactory::new(),
            'receivedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'sentBy' => self::faker()->text(255),
        ];
    }
}
