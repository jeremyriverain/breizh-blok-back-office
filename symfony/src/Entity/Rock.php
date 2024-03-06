<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\RockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RockRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['Rock:read']],
    operations: [
        new Get(),
        new GetCollection(),
    ],
    paginationClientEnabled: true,
)]
class Rock
{
    use TimestampableTrait;
    use BlameableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: "integer")]
    private ?int $id;

    #[ORM\OneToOne(targetEntity: GeoPoint::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\Valid()]
    #[Groups(["Rock:read", "Boulder:read", 'Boulder:map'])]
    private ?GeoPoint $location;

    #[ORM\ManyToOne(targetEntity: BoulderArea::class, inversedBy: "rocks")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Groups(["Boulder:read"])]
    private ?BoulderArea $boulderArea;

    /**
     * @var Collection<int, Boulder>|Boulder[]
     */
    #[ORM\OneToMany(targetEntity: Boulder::class, mappedBy: "rock", orphanRemoval: true)]
    #[Groups(["Rock:read"])]
    private Collection $boulders;

    /**
     * @var Collection<int, Media>|Media[]
     */
    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: "rock", cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Assert\Valid()]
    #[Groups(["Rock:read"])]
    private Collection $pictures;

    public function __construct()
    {
        $this->location = new GeoPoint();
        $this->boulders = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->boulderArea . ' #' . $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocation(): ?GeoPoint
    {
        return $this->location;
    }

    public function setLocation(GeoPoint $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getBoulderArea(): ?BoulderArea
    {
        return $this->boulderArea;
    }

    public function setBoulderArea(?BoulderArea $boulderArea): self
    {
        $this->boulderArea = $boulderArea;

        return $this;
    }

    /**
     * @return Collection<int, Boulder>|Boulder[]
     */
    public function getBoulders(): Collection
    {
        return $this->boulders;
    }

    public function addBoulder(Boulder $boulder): self
    {
        if (!$this->boulders->contains($boulder)) {
            $this->boulders[] = $boulder;
            $boulder->setRock($this);
        }

        return $this;
    }

    public function removeBoulder(Boulder $boulder): self
    {
        if ($this->boulders->removeElement($boulder)) {
            // set the owning side to null (unless already changed)
            if ($boulder->getRock() === $this) {
                $boulder->setRock(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Media>|Media[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Media $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setRock($this);
        }

        return $this;
    }

    public function removePicture(Media $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getRock() === $this) {
                $picture->setRock(null);
            }
        }

        return $this;
    }
}
