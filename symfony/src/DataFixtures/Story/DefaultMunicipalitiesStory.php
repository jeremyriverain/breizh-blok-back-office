<?php

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\MunicipalityFactory;
use Zenstruck\Foundry\Story;

final class DefaultMunicipalitiesStory extends Story
{
    public const LAMPAUL_PLOUARZEL = 'Lampaul-Plouarzel';
    public const KERLOUAN = 'Kerlouan';

    public function build(): void
    {
        MunicipalityFactory::createOne([
            'name' => self::LAMPAUL_PLOUARZEL, 
        ]);

        MunicipalityFactory::createOne([
            'name' => self::KERLOUAN, 
        ]);
    }
}
