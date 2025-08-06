<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\GradeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GradeRepository::class)]
#[UniqueEntity('name')]
#[ApiResource(
    normalizationContext: ['groups' => ['Grade:read']],
    operations: [
        new Get(),
        new GetCollection(),
    ],
    paginationClientEnabled: true
)]
#[ApiFilter(OrderFilter::class, properties: ['name' => 'ASC'])]
#[ApiFilter(ExistsFilter::class, properties: ['boulders'])]
class Grade
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 10)]
    #[Assert\NotBlank()]
    #[Assert\Length(max: 10)]
    #[Groups(['read'])]
    private ?string $name;

    /**
     * @var Collection<int, Boulder>|Boulder[]
     */
    #[ORM\OneToMany(targetEntity: Boulder::class, mappedBy: 'grade')]
    private Collection $boulders;

    public function __toString()
    {
        return $this->name ?? '';
    }

    public function __construct()
    {
        $this->boulders = new ArrayCollection();
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
            $boulder->setGrade($this);
        }

        return $this;
    }

    public function removeBoulder(Boulder $boulder): self
    {
        if ($this->boulders->removeElement($boulder)) {
            // set the owning side to null (unless already changed)
            if ($boulder->getGrade() === $this) {
                $boulder->setGrade(null);
            }
        }

        return $this;
    }
}
