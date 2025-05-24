<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\BoulderArea;
use App\Entity\GeoPoint;
use App\Entity\Municipality;
use PHPUnit\Framework\TestCase;

final class MunicipalityTest extends TestCase
{
    public function testGetBoundaries(): void
    {
        $boulderArea = new BoulderArea();
        $boulderArea->setCentroid(new GeoPoint('90', '80'));

        $boulderArea2 = new BoulderArea();
        $boulderArea2->setCentroid(new GeoPoint('60', '-20'));

        $boulderArea3 = new BoulderArea();

        $municipality = new Municipality();
        $municipality->addBoulderArea($boulderArea);
        $municipality->addBoulderArea($boulderArea2);
        $municipality->addBoulderArea($boulderArea3);

        $this->assertEquals(count($municipality->getBoundaries()), 2);
        $this->assertEquals($municipality->getBoundaries()[0], $boulderArea->getCentroid());
        $this->assertEquals($municipality->getBoundaries()[1], $boulderArea2->getCentroid());

        $municipality2 = new Municipality();
        $this->assertEquals($municipality2->getBoundaries(), []);
    }
}
