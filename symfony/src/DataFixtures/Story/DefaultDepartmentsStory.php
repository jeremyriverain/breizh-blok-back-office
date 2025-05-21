<?php

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\DepartmentFactory;
use App\Entity\Municipality;
use Zenstruck\Foundry\Story;

use function Zenstruck\Foundry\Persistence\repository;

final class DefaultDepartmentsStory extends Story
{
    public const FINISTERE = 'Finistère';

    public function build(): void
    {
        $municipalityRepository = repository(Municipality::class);
        DepartmentFactory::createOne([
            'name' => self::FINISTERE,
            'municipalities' => [
                $municipalityRepository->findOneBy(['name' => DefaultMunicipalitiesStory::LAMPAUL_PLOUARZEL]),
                $municipalityRepository->findOneBy(['name' => DefaultMunicipalitiesStory::KERLOUAN]),
            ]
        ]);
    }
}
