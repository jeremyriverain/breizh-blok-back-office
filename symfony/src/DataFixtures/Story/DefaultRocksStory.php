<?php

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\BoulderFactory;
use App\DataFixtures\Factory\GeoPointFactory;
use App\DataFixtures\Factory\MediaFactory;
use App\DataFixtures\Factory\RockFactory;
use App\Entity\BoulderArea;
use App\Entity\Grade;
use App\Entity\HeightBoulder;
use App\Entity\Municipality;
use App\Services\CentroidCalculator;
use Zenstruck\Foundry\Story;

use function Zenstruck\Foundry\Persistence\flush_after;
use function Zenstruck\Foundry\Persistence\repository;

final class DefaultRocksStory extends Story
{
    public function build(): void
    {
        $boulderAreaRepository = repository(BoulderArea::class);

        $gradeRepository = repository(Grade::class);
        $heightBoulderRepository = repository(HeightBoulder::class);

         
        $rock1 = RockFactory::createOne([
            'location' => GeoPointFactory::createOne([
                'latitude' => "48.67314974843626",
                'longitude' => "-4.358081945162471",
            ]),
            'boulderArea' => $boulderAreaRepository->findOneBy([
                'name' => DefaultBoulderAreasStory::CREMIOU,
            ]),
            'pictures' => [ 
                MediaFactory::createOne([
                'filePath' => 'boulder1.jpg'
                ]
                )
            ],
        ],
        );

        BoulderFactory::createOne([
            'name' => 'Stone',
            'grade' => $gradeRepository->findOneBy(['name' => '5']),
            'rock' => $rock1->_real(),
            'description' => 'Un rétablissement sur 2 bonnes réglettes',
            'isUrban' => true,
            'height' => $heightBoulderRepository->findOneBy(['min' => 0, 'max' => 3]),
        ]);
        BoulderFactory::createOne([
            'name' => 'Monkey',
            'grade' => $gradeRepository->findOneBy(['name' => '6a']),
            'rock' => $rock1->_real(),
            'description' => 'Remonter l\'angle sur granit lisse et surprenant',
        ]);

        $rock2 = RockFactory::createOne([
            'location' => GeoPointFactory::createOne([
                'latitude' => "48.67331447037122",
                'longitude' => "-4.357883461703047",
            ]),
            'boulderArea' => $boulderAreaRepository->findOneBy([
                'name' => DefaultBoulderAreasStory::CREMIOU,
            ]),
            
            
        ]);

        BoulderFactory::createOne([
            'name' => 'L\'essai',
            'grade' => $gradeRepository->findOneBy(['name' => '6c']),
            'rock' => $rock2->_real(),
            'description' => 'De vagues réglettes en main droite, un mauvais pied et... lancer',
        ]);

        $rock3 = RockFactory::createOne([
            'location' => GeoPointFactory::createOne([
                'latitude' => "48.6694591366599",
                'longitude' => "-4.371922069116468",
            ]),
            'boulderArea' => $boulderAreaRepository->findOneBy([
                'name' => DefaultBoulderAreasStory::MENEZ_HAM,
            ]),
        ]);

        BoulderFactory::createOne([
            'name' => 'Les cornes du diable',
            'grade' => $gradeRepository->findOneBy(['name' => '6a']),
            'rock' => $rock3->_real(),
            'description' => 'Départ assis, remonter l\'arête',
        ]);

        $boulderAreaRepository = repository(BoulderArea::class);
        $boulderAreaCremiou = $boulderAreaRepository->findOneBy(['name' => DefaultBoulderAreasStory::CREMIOU]);
        $boulderAreaMenezHam = $boulderAreaRepository->findOneBy(['name' => DefaultBoulderAreasStory::MENEZ_HAM]);

        $municipalityRepository = repository(Municipality::class);
        $lampaulPlouarzel = $municipalityRepository->findOneBy(['name' => DefaultMunicipalitiesStory::LAMPAUL_PLOUARZEL]);
        $kerlouan = $municipalityRepository->findOneBy(['name' => DefaultMunicipalitiesStory::KERLOUAN]);

        if ($boulderAreaCremiou === null || 
            $boulderAreaMenezHam === null || 
            $lampaulPlouarzel === null || 
            $kerlouan === null
        ) {
            throw new \Exception('there is at least 1 object that is not found');
        }
        
        $boulderAreaCremiou->setCentroid(CentroidCalculator::getCentroid($boulderAreaCremiou->getBoundaries()));
        $boulderAreaMenezHam->setCentroid(CentroidCalculator::getCentroid($boulderAreaMenezHam->getBoundaries()));

        $lampaulPlouarzel->setCentroid(CentroidCalculator::getCentroid($lampaulPlouarzel->getBoundaries()));
        $kerlouan->setCentroid(CentroidCalculator::getCentroid($kerlouan->getBoundaries()));
    }
}
