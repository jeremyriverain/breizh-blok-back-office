<?php

namespace App\DataFixtures;

use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DepartmentFixtures extends Fixture
{

    public const FINISTERE = 'finistere';

    public function load(ObjectManager $manager): void
    {
        $finistere = new Department();
        $finistere->setName('Finistère');
        $manager->persist($finistere);

        $manager->flush();

        $this->addReference(self::FINISTERE, $finistere);
    }
}
