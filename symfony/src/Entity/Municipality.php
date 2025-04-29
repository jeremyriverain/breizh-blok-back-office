<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Interfaces\IZone;
use App\Repository\MunicipalityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MunicipalityRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['Municipality:read']],
    operations: [
        new Get(
            normalizationContext: ['groups' => ['Municipality:item-get']],
        ),
        new GetCollection(),
    ]
)]
#[UniqueEntity(fields: ['name', 'department'], ignoreNull: false)]
class Municipality implements IZone
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: "integer")]
    private ?int $id;

    #[ORM\Column(type: "string", length: 150)]
    #[Assert\NotBlank()]
    #[Assert\Length(max: 150)]
    #[Groups(["Boulder:read", "Department:read", "BoulderArea:read", "Municipality:read"])]
    private ?string $name;

    /**
     * @var Collection<int, BoulderArea>|BoulderArea[]
     */
    #[ORM\OneToMany(targetEntity: BoulderArea::class, mappedBy: "municipality", orphanRemoval: true)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    #[Groups(["Department:read", "Municipality:read"])]
    private $boulderAreas;

    #[ORM\OneToOne(targetEntity: GeoPoint::class, cascade: ['persist', 'remove'])]
    #[Assert\Valid()]
    #[Groups(["Municipality:item-get"])]
    private ?GeoPoint $centroid = null;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'municipalities')]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    private ?Department $department;

    public function __construct()
    {
        $this->boulderAreas = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name ?? '';
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
     * @return Collection<int, BoulderArea>|BoulderArea[]
     */
    public function getBoulderAreas(): Collection
    {
        return $this->boulderAreas;
    }

    public function addBoulderArea(BoulderArea $boulderArea): self
    {
        if (!$this->boulderAreas->contains($boulderArea)) {
            $this->boulderAreas[] = $boulderArea;
            $boulderArea->setMunicipality($this);
        }

        return $this;
    }

    public function removeBoulderArea(BoulderArea $boulderArea): self
    {
        if ($this->boulderAreas->removeElement($boulderArea)) {
            // set the owning side to null (unless already changed)
            if ($boulderArea->getMunicipality() === $this) {
                $boulderArea->setMunicipality(null);
            }
        }

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
        $geoPoints = array_map(function ($boulderArea) {
            /**
             * @var BoulderArea $boulderArea
             */
            return $boulderArea->getCentroid();
        }, $this->boulderAreas->filter(
            function ($b) {
                /**
                 * @var BoulderArea $b
                 */
                return $b->getCentroid() !== null;
            }
        )->toArray());

        return $geoPoints;
    }

    /**
     * @return array<int, Rock>
     */
    public function getRocks(): array
    {
        return array_reduce(
            $this->boulderAreas->toArray(),
            function ($rocks, $boulderArea) {
                /**
                 * @var \App\Entity\BoulderArea $boulderArea
                 * @var \App\Entity\Rock[] $rocks
                 */
                return [...$rocks, ...$boulderArea->getRocks()->toArray()];
            },
            []
        );
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }
}
