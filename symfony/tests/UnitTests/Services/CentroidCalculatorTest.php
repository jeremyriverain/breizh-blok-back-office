<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Services;

use App\Entity\GeoPoint;
use App\Services\CentroidCalculator;
use PHPUnit\Framework\TestCase;

final class CentroidCalculatorTest extends TestCase
{
    public function testCentroid(): void
    {
        $centroid = CentroidCalculator::getCentroid([
            new GeoPoint(strval(20), strval(-10)),
            new GeoPoint(strval(40), strval(20)),
            new GeoPoint(strval(60), strval(20))
        ]);
        $this->assertEquals(40, $centroid?->getLatitude());
        $this->assertEquals(10, $centroid?->getLongitude());

        $centroid = CentroidCalculator::getCentroid([]);
        $this->assertEquals(null, $centroid);
    }
}
