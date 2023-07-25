<?php

namespace App\DataFixtures;

use App\Entity\BoulderArea;
use App\Entity\Municipality;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BoulderAreaFixtures extends Fixture implements DependentFixtureInterface
{
    public const BOULDER_AREA_CREMIOU = 'boulder-area-cremiou';
    public const BOULDER_AREA_MENEZ_HAM = 'boulder-area-menez-ham';

    public function load(ObjectManager $manager): void
    {
        $cremiou = new BoulderArea();
        $cremiou->setName('Cremiou');
        $this->getMunicipalityReference(MunicipalityFixtures::KERLOUAN)->addBoulderArea($cremiou);
        $manager->persist($cremiou);


        $petit_paradis = new BoulderArea();
        $petit_paradis->setName('Petit paradis');
        $this->getMunicipalityReference(MunicipalityFixtures::KERLOUAN)->addBoulderArea($petit_paradis);
        $manager->persist($petit_paradis);

        $menez_ham = new BoulderArea();
        $menez_ham->setName('Menez Ham');
        $this->getMunicipalityReference(MunicipalityFixtures::KERLOUAN)->addBoulderArea($menez_ham);
        $manager->persist($menez_ham);

        $la_riviere = new BoulderArea();
        $la_riviere->setName('La rivière');
        $this->getMunicipalityReference(MunicipalityFixtures::KERLOUAN)->addBoulderArea($la_riviere);
        $manager->persist($la_riviere);

        $bivouac = new BoulderArea();
        $bivouac->setName('Bivouac');
        $this->getMunicipalityReference(MunicipalityFixtures::KERLOUAN)->addBoulderArea($bivouac);
        $manager->persist($bivouac);

        $phare = new BoulderArea();
        $phare->setName('Le phare');
        $this->getMunicipalityReference(MunicipalityFixtures::LAMPAUL_PLOUARZEL)->addBoulderArea($phare);
        $manager->persist($phare);

        $manager->flush();

        $this->addReference(self::BOULDER_AREA_CREMIOU, $cremiou);
        $this->addReference(self::BOULDER_AREA_MENEZ_HAM, $menez_ham);
    }

    private function getMunicipalityReference(string $value): Municipality
    {
        $ref = $this->getReference($value);
        return $ref instanceof Municipality ? $ref : throw new \Exception("value should be an instance of Municipality");
    }

    public function getDependencies()
    {
        return [
            MunicipalityFixtures::class,
        ];
    }
}
