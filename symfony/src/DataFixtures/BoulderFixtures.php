<?php

namespace App\DataFixtures;

use App\Entity\Boulder;
use App\Entity\Grade;
use App\Entity\HeightBoulder;
use App\Entity\Rock;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BoulderFixtures extends Fixture implements DependentFixtureInterface
{

    public const BOULDER_STONE = 'boulder-stone';
    public const BOULDER_MONKEY = 'boulder-monkey';

    public function load(ObjectManager $manager): void
    {
        $stone = new Boulder();
        $stone->setName('Stone');
        $stone->setGrade($this->getGradeReference('grade_5'));
        $stone->setRock($this->getRockReference(RockFixtures::ROCK_CREMIOU));
        $stone->setDescription('Un rétablissement sur 2 bonnes réglettes');
        $stone->setIsUrban(true);
        $stone->setHeight($this->getHeightReference(HeightBoulderFixtures::LESS_THAN_3));
        $manager->persist($stone);

        $monkey = new Boulder();
        $monkey->setName('Monkey');
        $monkey->setGrade($this->getGradeReference('grade_6a'));
        $monkey->setRock($this->getRockReference(RockFixtures::ROCK_CREMIOU));
        $monkey->setDescription('Remonter l\'angle sur granit lisse et surprenant');
        $manager->persist($monkey);

        $lessai = new Boulder();
        $lessai->setName('L\'essai');
        $lessai->setGrade($this->getGradeReference('grade_6c'));
        $lessai->setRock($this->getRockReference(RockFixtures::ROCK_CREMIOU_2));
        $lessai->setDescription('De vagues réglettes en main droite, un mauvais pied et... lancer');
        $manager->persist($lessai);

        $lesCornesDuDiable = new Boulder();
        $lesCornesDuDiable->setName('Les cornes du diable');
        $lesCornesDuDiable->setGrade($this->getGradeReference('grade_6a'));
        $lesCornesDuDiable->setRock($this->getRockReference(RockFixtures::ROCK_MENEZ_HAM));
        $lesCornesDuDiable->setDescription('Départ assis, remonter l\'arête');
        $manager->persist($lesCornesDuDiable);

        $manager->flush();

        $this->addReference(self::BOULDER_STONE, $stone);
        $this->addReference(self::BOULDER_MONKEY, $monkey);
    }

    private function getRockReference(string $value): Rock
    {
        return $this->getReference($value, 'App\Entity\Rock');
    }

    private function getGradeReference(string $value): Grade
    {
        return $this->getReference($value, 'App\Entity\Grade');
    }

    private function getHeightReference(string $value): HeightBoulder
    {
        return $this->getReference($value, 'App\Entity\HeightBoulder');
    }

    public function getDependencies(): array
    {
        return [
            GradeFixtures::class,
            RockFixtures::class,
        ];
    }
}
