<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use App\Filters\Api\BoulderTermFilter;
use App\Interfaces\ContainsMediaInterface;
use App\Repository\BoulderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: BoulderRepository::class)]
#[ORM\Index(name: "name_idx", columns: ["name"])]
#[ApiResource(
    normalizationContext: ['groups' => ['Boulder:read']],
    collectionOperations: [
        'get',
    ],
    itemOperations: [
        'get',
    ],
    paginationClientEnabled: true,
)]
#[ApiFilter(OrderFilter::class, properties: ['id', 'name', 'grade.name' => ['nulls_comparison' => OrderFilter::NULLS_LARGEST,]])]
#[ApiFilter(SearchFilter::class, properties: [
    'id' => 'exact',
    'name' => 'iword_start',
    'grade.name' => 'exact',
    'rock.boulderArea.id' => 'exact',
    'rock.boulderArea.name' => 'exact',
    'rock.boulderArea.municipality.name' => 'exact',
    'rock.id' => 'exact',
])]
#[ApiFilter(BoulderTermFilter::class)]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
#[ApiFilter(GroupFilter::class, arguments: ['overrideDefaultGroups' => true, 'whitelist' => ['Boulder:map']])]
class Boulder implements ContainsMediaInterface
{

    use TimestampableTrait;
    use BlameableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: "integer")]
    #[Groups(['Boulder:map'])]
    private ?int $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(max: 255)]
    #[Groups(["Rock:read", 'Boulder:read', 'LineBoulder:read'])]
    private ?string $name;

    #[ORM\ManyToOne(targetEntity: Grade::class, inversedBy: "boulders")]
    #[Groups(['Boulder:read', "Municipality:read"])]
    private ?Grade $grade;

    #[ORM\ManyToOne(targetEntity: Rock::class, inversedBy: "boulders")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Groups(['Boulder:read', 'Boulder:map'])]
    private ?Rock $rock;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Groups(['Boulder:item-get'])]
    private ?string $description;

    /**
     * @var Collection<int, LineBoulder>|LineBoulder[]
     */
    #[ORM\OneToMany(targetEntity: LineBoulder::class, mappedBy: "boulder", orphanRemoval: true)]
    #[Groups(['Boulder:read'])]
    private Collection $lineBoulders;

    #[Gedmo\Blameable(on: "create")]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    private ?User $createdBy;

    #[Gedmo\Blameable(on: "update")]
    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    private ?User $updatedBy = null;

    public function __construct()
    {
        $this->lineBoulders = new ArrayCollection();
    }

    /**
     * @return array<int, string>
     */
    public function getMediaAttributes(): array
    {
        $res = [];

        foreach ($this->lineBoulders as $key => $value) {
            $res[] = 'lineBoulders[' . $key . '].rockImage';
        }
        return $res;
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

    public function getGrade(): ?Grade
    {
        return $this->grade;
    }

    public function setGrade(?Grade $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    public function getRock(): ?Rock
    {
        return $this->rock;
    }

    public function setRock(?Rock $rock): self
    {
        $this->rock = $rock;

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

    /**
     * @return Collection<int, LineBoulder>|LineBoulder[]
     */
    public function getLineBoulders(): Collection
    {
        return $this->lineBoulders;
    }

    public function addLineBoulder(LineBoulder $lineBoulder): self
    {
        if (!$this->lineBoulders->contains($lineBoulder)) {
            $this->lineBoulders[] = $lineBoulder;
            $lineBoulder->setBoulder($this);
        }

        return $this;
    }

    public function removeLineBoulder(LineBoulder $lineBoulder): self
    {
        if ($this->lineBoulders->removeElement($lineBoulder)) {
            // set the owning side to null (unless already changed)
            if ($lineBoulder->getBoulder() === $this) {
                $lineBoulder->setBoulder(null);
            }
        }

        return $this;
    }



}
