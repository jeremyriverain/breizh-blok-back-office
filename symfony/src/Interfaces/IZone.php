<?php

namespace App\Interfaces;

use App\Entity\GeoPoint;

interface IZone
{
    /**
     * @return array<int, GeoPoint>
     */
    public function getBoundaries(): array;

    public function getCentroid(): ?GeoPoint;

    public function setCentroid(?GeoPoint $centroid): self;
}
