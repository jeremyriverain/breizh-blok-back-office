<?php

namespace App\DataFixtures;

use App\DataFixtures\BoulderAreaFixtures as DataFixturesBoulderAreaFixtures;
use App\Entity\BoulderArea;
use App\Entity\GeoPoint;
use App\Entity\Media;
use App\Entity\Rock;
use App\Services\CentroidCalculator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RockFixtures extends Fixture implements DependentFixtureInterface
{
    public const ROCK_CREMIOU = 'rock_cremiou';
    public const ROCK_CREMIOU_2 = 'rock_cremiou_2';
    public const ROCK_MENEZ_HAM = 'rock_menez_ham';

    public function load(ObjectManager $manager): void
    {
        $boulderAreaCremiou = $this->getBoulderAreaReference(DataFixturesBoulderAreaFixtures::BOULDER_AREA_CREMIOU);
        $boulderAreaMenezHam = $this->getBoulderAreaReference(DataFixturesBoulderAreaFixtures::BOULDER_AREA_MENEZ_HAM);
        $rockCremiou = new Rock();
        $rockCremiou->setLocation(new GeoPoint("48.67314974843626", "-4.358081945162471"));
        $rockCremiou->setBoulderArea($boulderAreaCremiou);
        $rockCremiou->addPicture($this->getMediaAreaReference(MediaFixtures::BOULDER_IMG));
        $manager->persist($rockCremiou);

        $rockCremiou2 = new Rock();
        $rockCremiou2->setLocation(new GeoPoint("48.67331447037122", "-4.357883461703047"));
        $rockCremiou2->setBoulderArea($boulderAreaCremiou);
        $manager->persist($rockCremiou2);


        $rockMenezHam = new Rock();
        $rockMenezHam->setLocation(new GeoPoint("48.6694591366599", "-4.371922069116468"));
        $rockMenezHam->setBoulderArea($boulderAreaMenezHam);
        $manager->persist($rockMenezHam);

        $manager->flush();

        $boulderAreaCremiou->setCentroid(CentroidCalculator::getCentroid($boulderAreaCremiou->getBoundaries()));
        $boulderAreaMenezHam->setCentroid(CentroidCalculator::getCentroid($boulderAreaMenezHam->getBoundaries()));
        $kerlouan = $boulderAreaCremiou->getMunicipality();
        $kerlouan?->setCentroid(CentroidCalculator::getCentroid($kerlouan->getBoundaries()));

        $manager->flush();

        $this->addReference(self::ROCK_CREMIOU, $rockCremiou);
        $this->addReference(self::ROCK_CREMIOU_2, $rockCremiou2);
        $this->addReference(self::ROCK_MENEZ_HAM, $rockMenezHam);
    }

    private function getBoulderAreaReference(string $value): BoulderArea
    {
        $ref = $this->getReference($value);
        return $ref instanceof BoulderArea ? $ref : throw new \Exception("value should be an instance of BoulderArea");
    }

    private function getMediaAreaReference(string $value): Media
    {
        $ref = $this->getReference($value);
        return $ref instanceof Media ? $ref : throw new \Exception("value should be an instance of Media");
    }

    public function getDependencies()
    {
        return [
            BoulderAreaFixtures::class,
            MediaFixtures::class
        ];
    }
}
