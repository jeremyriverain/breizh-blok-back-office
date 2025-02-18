<?php

namespace App\DataFixtures;

use App\Entity\HeightBoulder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class HeightBoulderFixtures extends Fixture
{

    public const LESS_THAN_3 = 'less-than-3';

    public function load(ObjectManager $manager): void
    {
        $lessThan3 = new HeightBoulder();
        $lessThan3->setMin(0);
        $lessThan3->setMax(3);
        $manager->persist($lessThan3);

        $between3And5 = new HeightBoulder();
        $between3And5->setMin(3);
        $between3And5->setMax(5);
        $manager->persist($between3And5);

        $moreThan5 = new HeightBoulder();
        $moreThan5->setMin(5);
        $manager->persist($moreThan5);

        $manager->flush();

        $this->addReference(self::LESS_THAN_3, $lessThan3);
    }
}
