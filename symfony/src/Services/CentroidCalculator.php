<?php

namespace App\Services;

use App\Entity\GeoPoint;
use App\Entity\Rock;
use Doctrine\ORM\EntityManagerInterface;

class CentroidCalculator
{
    /**
     * @param array<int, GeoPoint> $geoPoints 
     */
    public static function getCentroid(array $geoPoints = []): GeoPoint | null
    {
        if (count($geoPoints) === 0) {
            return null;
        }
        $latitude = 0;
        $longitude = 0;
        foreach ($geoPoints as $geoPoint) {
            /**
             * @var GeoPoint $geoPoint 
             */
            $latitude += $geoPoint->getLatitude();
            $longitude += $geoPoint->getLongitude();
        }

        $divider = count($geoPoints);
        $latitude /= $divider;
        $longitude /= $divider;

        return new GeoPoint(strval($latitude), strval($longitude));
    }

    /** @phpstan-ignore-next-line */
    public static function onRockUpdate(EntityManagerInterface $em, $entity): void
    {
        if (!$entity instanceof Rock || !$entity->getBoulderArea()) {
            return;
        }
        $boulderArea = $entity->getBoulderArea();
        if ($boulderArea->getCentroid()) {
            $em->remove($boulderArea->getCentroid());
        }

        $boulderArea->setCentroid(self::getCentroid($boulderArea->getBoundaries()));

        $municipality = $boulderArea->getMunicipality();

        if (!$municipality) {
            $em->flush();
            return;
        }

        if ($municipality->getCentroid()) {
            $em->remove($municipality->getCentroid());
        }
        $municipality->setCentroid(CentroidCalculator::getCentroid($municipality->getBoundaries()));
        $em->flush();
    }
}
