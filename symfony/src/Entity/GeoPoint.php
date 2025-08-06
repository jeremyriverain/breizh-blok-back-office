<?php

namespace App\Entity;

use App\Repository\GeoPointRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GeoPointRepository::class)]
class GeoPoint
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'float', precision: 10, scale: 8)]
    #[Assert\NotBlank()]
    #[Assert\Range(min: -90, max: 90)]
    #[Groups(['read', 'Boulder:map', 'BoulderArea:read', 'BoulderFeedback:write'])]
    private ?string $latitude;

    #[ORM\Column(type: 'float', precision: 10, scale: 7)]
    #[Assert\NotBlank()]
    #[Assert\Range(min: -180, max: 180)]
    #[Groups(['read', 'Boulder:map', 'BoulderArea:read', 'BoulderFeedback:write'])]
    private ?string $longitude;

    public function __construct(?string $latitude = null, ?string $longitude = null)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function __toString()
    {
        return $this->latitude.', '.$this->longitude;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(?string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(?string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }
}
