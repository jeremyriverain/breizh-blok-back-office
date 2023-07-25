<?php

namespace App\DataFixtures;

use App\Entity\Grade;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GradeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $grades = ['4', '5', '5+', '6a', '6a+', '6b', '6b+', '6c', '6c+', '7a', '7a+', '7b', '7b+', '7c', '7c+', '8a', '8a+', '8b', '8b+', '8c', '8c+', '9a'];

        foreach ($grades as $grade) {
            $g = new Grade();
            $g->setName($grade);
            $this->addReference('grade_' . $grade, $g);
            $manager->persist($g);
        }

        $manager->flush();
    }
}
