<?php

namespace App\DataFixtures;

use App\Entity\Department;
use App\Entity\Municipality;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MunicipalityFixtures extends Fixture implements DependentFixtureInterface
{

    public const KERLOUAN = 'kerlouan';
    public const LAMPAUL_PLOUARZEL = 'lampaul_plouarzel';

    public function load(ObjectManager $manager): void
    {
        $lampaul_plouarzel = new Municipality();
        $lampaul_plouarzel->setName('Lampaul-Plouarzel');
        $lampaul_plouarzel->setDepartment($this->getDepartmentReference(DepartmentFixtures::FINISTERE));
        $manager->persist($lampaul_plouarzel);

        $kerlouan = new Municipality();
        $kerlouan->setName('Kerlouan');
        $kerlouan->setDepartment($this->getDepartmentReference(DepartmentFixtures::FINISTERE));
        $manager->persist($kerlouan);

        $manager->flush();

        $this->addReference(self::LAMPAUL_PLOUARZEL, $lampaul_plouarzel);
        $this->addReference(self::KERLOUAN, $kerlouan);
    }

    private function getDepartmentReference(string $value): Department
    {
        return $this->getReference($value, 'App\Entity\Department');
    }

    public function getDependencies(): array
    {
        return [
            DepartmentFixtures::class
        ];
    }
}
