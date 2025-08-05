<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\DepartmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['Department:read']],
    operations: [
        new Get(),
        new GetCollection(),
    ],
    paginationClientEnabled: true,
)]
#[ApiFilter(OrderFilter::class, properties: ['name'])]
class Department
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(max: 255)]
    #[Groups(["Department:read"])]
    private ?string $name;

    /**
     * @var Collection<int, Municipality>|Municipality[]
     */
    #[ORM\OneToMany(mappedBy: 'department', targetEntity: Municipality::class)]
    #[ORM\OrderBy(['name' => 'ASC'])]
    #[Groups(["Department:read"])]
    private $municipalities;

    public function __toString()
    {
        return $this->name ?? '';
    }

    public function __construct()
    {
        $this->municipalities = new ArrayCollection();
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
     * @return Collection<int, Municipality>|Municipality[]
     */
    public function getMunicipalities(): Collection
    {
        return $this->municipalities;
    }

    public function addMunicipality(Municipality $municipality): self
    {
        if (!$this->municipalities->contains($municipality)) {
            $this->municipalities[] = $municipality;
            $municipality->setDepartment($this);
        }

        return $this;
    }

    public function removeMunicipality(Municipality $municipality): self
    {
        if ($this->municipalities->removeElement($municipality)) {
            // set the owning side to null (unless already changed)
            if ($municipality->getDepartment() === $this) {
                $municipality->setDepartment(null);
            }
        }

        return $this;
    }
}
