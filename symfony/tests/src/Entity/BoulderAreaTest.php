<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\Boulder;
use App\Entity\BoulderArea;
use App\Entity\GeoPoint;
use App\Entity\Rock;
use App\Entity\Grade;
use PHPUnit\Framework\TestCase;

final class BoulderAreaTest extends TestCase
{

    public function testGetBoundaries(): void
    {
        $rock1 = new Rock();
        $rock1->setLocation(new GeoPoint('90', '80'));

        $rock2 = new Rock();
        $rock2->setLocation(new GeoPoint('60', '-20'));

        $boulderArea = new BoulderArea();
        $boulderArea->addRock($rock1);
        $boulderArea->addRock($rock2);

        $this->assertEquals(count($boulderArea->getBoundaries()), 2);
        $this->assertEquals($boulderArea->getBoundaries()[0], $rock1->getLocation());
        $this->assertEquals($boulderArea->getBoundaries()[1], $rock2->getLocation());

        $boulderArea2 = new BoulderArea();
        $this->assertEquals($boulderArea2->getBoundaries(), []);
    }

    public function testGetBouldersSortedByName(): void
    {
        $cBoulder = new Boulder();
        $cBoulder->setName('c');
        $aBoulder = new Boulder();
        $aBoulder->setName('a');

        $rock = new Rock();
        $rock->addBoulder($cBoulder);
        $rock->addBoulder($aBoulder);
        $boulderArea = new BoulderArea();
        $boulderArea->addRock($rock);

        $this->assertEquals($boulderArea->getBoulders(), [$cBoulder, $aBoulder]);
        $this->assertEquals($boulderArea->getBouldersSortedByName(), [$aBoulder, $cBoulder]);
    }

    public function testGetBouldersSortedByGrade(): void
    {
        $boulderNoGrade = (new Boulder())->setGrade(null);
        $boulder5aPlus = (new Boulder())->setGrade((new Grade())->setName('5a+'));
        $boulder4a = (new Boulder())->setGrade((new Grade())->setName('4a'));
        $boulder5a = (new Boulder())->setGrade((new Grade())->setName('5a'));

        $rock = new Rock();
        $rock->addBoulder($boulderNoGrade);
        $rock->addBoulder($boulder5aPlus);
        $rock->addBoulder($boulder4a);
        $rock->addBoulder($boulder5a);

        $boulderArea = new BoulderArea();
        $boulderArea->addRock($rock);

        $this->assertEquals($boulderArea->getBoulders(), [$boulderNoGrade, $boulder5aPlus, $boulder4a, $boulder5a]);
        $this->assertEquals($boulderArea->getBouldersSortedByGrade(), [$boulder4a, $boulder5a, $boulder5aPlus, $boulderNoGrade]);
    }

    public function testGetNumberOfBouldersGroupedByGrade(): void
    {
        $boulderNoGrade = (new Boulder())->setGrade(null);
        $boulder5aPlus = (new Boulder())->setGrade((new Grade())->setName('5a+'));
        $boulder4a = (new Boulder())->setGrade((new Grade())->setName('4a'));
        $boulder4a2 = (new Boulder())->setGrade((new Grade())->setName('4a'));
        $boulder5a = (new Boulder())->setGrade((new Grade())->setName('5a'));

        $rock = new Rock();
        $rock->addBoulder($boulderNoGrade);
        $rock->addBoulder($boulder5aPlus);
        $rock->addBoulder($boulder4a);
        $rock->addBoulder($boulder4a2);
        $rock->addBoulder($boulder5a);

        $boulderArea = new BoulderArea();
        $boulderArea->addRock($rock);

        $this->assertEquals($boulderArea->getBoulders(), [$boulderNoGrade, $boulder5aPlus, $boulder4a, $boulder4a2, $boulder5a]);
        $this->assertEquals($boulderArea->getNumberOfBouldersGroupedByGrade(), ['4a' => 2, '5a' => 1, '5a+' => 1]);
    }
}
