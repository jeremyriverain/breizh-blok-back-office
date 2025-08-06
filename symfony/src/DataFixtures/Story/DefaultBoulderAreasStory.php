<?php

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\BoulderAreaFactory;
use App\DataFixtures\Factory\GeoPointFactory;
use App\Entity\Municipality;
use Zenstruck\Foundry\Story;

use function Zenstruck\Foundry\Persistence\repository;

final class DefaultBoulderAreasStory extends Story
{
    public const CREMIOU = 'Cremiou';
    public const MENEZ_HAM = 'Menez Ham';
    public const LE_PHARE = 'Le phare';

    public function build(): void
    {
        $municipalityRepository = repository(Municipality::class);

        BoulderAreaFactory::createOne([
            'name' => self::CREMIOU,
            'municipality' => $municipalityRepository->findOneBy(
                ['name' => DefaultMunicipalitiesStory::KERLOUAN]
            ),
            'parkingLocation' => GeoPointFactory::createOne([
                'latitude' => '48.6734',
                'longitude' => '-4.35788',
            ]),
        ]);

        BoulderAreaFactory::createOne([
            'name' => 'Petit paradis',
            'municipality' => $municipalityRepository->findOneBy(
                ['name' => DefaultMunicipalitiesStory::KERLOUAN]
            ),
        ]);

        BoulderAreaFactory::createOne([
            'name' => self::MENEZ_HAM,
            'municipality' => $municipalityRepository->findOneBy(
                ['name' => DefaultMunicipalitiesStory::KERLOUAN]
            ),
        ]);

        BoulderAreaFactory::createOne([
            'name' => 'La rivière',
            'municipality' => $municipalityRepository->findOneBy(
                ['name' => DefaultMunicipalitiesStory::KERLOUAN]
            ),
        ]);

        BoulderAreaFactory::createOne([
            'name' => 'Bivouac',
            'municipality' => $municipalityRepository->findOneBy(
                ['name' => DefaultMunicipalitiesStory::KERLOUAN]
            ),
        ]);

        BoulderAreaFactory::createOne([
            'name' => 'Le phare',
            'municipality' => $municipalityRepository->findOneBy(
                ['name' => DefaultMunicipalitiesStory::LAMPAUL_PLOUARZEL]
            ),
        ]);
    }
}
