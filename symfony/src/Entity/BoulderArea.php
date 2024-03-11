<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Interfaces\IBlameable;
use App\Interfaces\ITimestampable;
use App\Interfaces\IZone;
use App\Repository\BoulderAreaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BoulderAreaRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['BoulderArea:read']],
    operations: [
        new Get(),
        new GetCollection(),
    ]
)]
class BoulderArea implements IZone, ITimestampable, IBlameable
{
    use TimestampableTrait;
    use BlameableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: "integer")]
    private ?int $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(max: 255)]
    #[Groups(["BoulderArea:read", "Boulder:read", "Department:read", "Municipality:read"])]
    private ?string $name;

    /**
     * @var Collection<int, Rock>|Rock[]
     */
    #[ORM\OneToMany(targetEntity: Rock::class, mappedBy: "boulderArea", orphanRemoval: true)]
    private $rocks;

    #[ORM\Column(type: "text", nullable: true)]
    #[Groups(["BoulderArea:read"])]
    private ?string $description;

    #[ORM\ManyToOne(targetEntity: Municipality::class, inversedBy: "boulderAreas")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Groups(["Boulder:read", "BoulderArea:read"])]
    private ?Municipality $municipality;

    #[ORM\OneToOne(targetEntity: GeoPoint::class, cascade: ['persist', 'remove'])]
    #[Assert\Valid()]
    #[Groups(["BoulderArea:item-get", "Municipality:item-get"])]
    private ?GeoPoint $centroid = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(["BoulderArea:item-get"])]
    private ?GeoPoint $parkingLocation = null;

    #[Groups(["Municipality:item-get"])]
    public ?Grade $lowestGrade = null;

    #[Groups(["Municipality:item-get"])]
    public ?Grade $highestGrade = null;

    #[Groups(["Municipality:item-get"])]
    public ?int $numberOfBoulders = null;

    public function __construct()
    {
        $this->rocks = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name ?? "";
    }

    /**
     * @return array<int, Boulder>
     */
    public function getBoulders(): array
    {
        return array_reduce($this->rocks->toArray(), function ($previous, $current) {
            /**
             * @var \App\Entity\Boulder[] $previous
             * @var \App\Entity\Rock $current
             */
            return [...$previous,  ...$current->getBoulders()->toArray()];
        }, []);
    }

    /**
     * @return array<int, Boulder>
     */
    public function getBouldersSortedByName(): array
    {
        $boulders = $this->getBoulders();
        sort($boulders, SORT_STRING);
        return $boulders;
    }

    /**
     * @return array<int, Boulder>
     */
    public function getBouldersSortedByGrade(): array
    {
        $boulders = $this->getBoulders();

        usort($boulders, function (Boulder $a, Boulder $b): int {
            $aGrade = $a->getGrade();
            $bGrade = $b->getGrade();
            if ($aGrade === null && $bGrade === null) {
                return 0;
            }
            if ($aGrade === null) {
                return 1;
            }
            if ($bGrade == null) {
                return -1;
            }
            return strcasecmp($aGrade->getName() ?? '', $bGrade->getName() ?? '');
        });
        return $boulders;
    }

    /**
     * @return array<string, int>
     */
    #[Groups(["BoulderArea:item-get"])]
    public function getNumberOfBouldersGroupedByGrade(): array
    {
        return array_reduce($this->getBouldersSortedByGrade(), function (array $carry, Boulder $item) {
            $grade = $item->getGrade();
            if (!$grade || !$grade->getName()) {
                return $carry;
            }

            if (!array_key_exists($grade->getName(), $carry)) {
                $carry[$grade->getName()] = 0;
            }

            $carry[$grade->getName()] = $carry[$grade->getName()] + 1;
            return $carry;
        }, []);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Rock>|Rock[]
     */
    public function getRocks(): Collection
    {
        return $this->rocks;
    }

    public function addRock(Rock $rock): self
    {
        if (!$this->rocks->contains($rock)) {
            $this->rocks[] = $rock;
            $rock->setBoulderArea($this);
        }

        return $this;
    }

    public function removeRock(Rock $rock): self
    {
        if ($this->rocks->removeElement($rock)) {
            // set the owning side to null (unless already changed)
            if ($rock->getBoulderArea() === $this) {
                $rock->setBoulderArea(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMunicipality(): ?Municipality
    {
        return $this->municipality;
    }

    public function setMunicipality(?Municipality $municipality): self
    {
        $this->municipality = $municipality;

        return $this;
    }

    public function getCentroid(): ?GeoPoint
    {
        return $this->centroid;
    }

    public function setCentroid(?GeoPoint $centroid): self
    {
        $this->centroid = $centroid;

        return $this;
    }

    public function getBoundaries(): array
    {
        /**
         * @var array<int, GeoPoint> $geoPoints
         */
        $geoPoints = array_map(function ($rock) {
            /**
             * @var Rock $rock
             */
            return $rock->getLocation();
        }, $this->rocks->toArray());

        return $geoPoints;
    }

    public function getParkingLocation(): ?GeoPoint
    {
        return $this->parkingLocation;
    }

    public function setParkingLocation(?GeoPoint $parkingLocation): self
    {
        $this->parkingLocation = $parkingLocation;

        return $this;
    }
}
