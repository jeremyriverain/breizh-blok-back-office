<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\LineBoulderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: LineBoulderRepository::class)]
#[UniqueEntity(
    fields: ['boulder', 'rockImage'],
    errorPath: 'boulder',
    message: 'The line of the boulder is already drawn on this rock picture',
)]
#[ApiResource(
    openapi: false,
    security: "is_granted('ROLE_USER')",
    normalizationContext: ['groups' => ['LineBoulder:read']],
    denormalizationContext: ['groups' => ['LineBoulder:write']],
    operations: [
        new GetCollection(uriTemplate: '/admin/line_boulders'),
        new Get(uriTemplate: '/admin/line_boulders/{id}'),
        new Put(uriTemplate: '/admin/line_boulders/{id}'),
        new Delete(uriTemplate: '/admin/line_boulders/{id}'),
        new Post(uriTemplate: '/admin/line_boulders', validationContext: ['groups' => ['Default', 'LineBoulder:collection-post']]),
    ],
)]
class LineBoulder
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: "integer")]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: Media::class, inversedBy: "lineBoulders")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Groups(["LineBoulder:read", "LineBoulder:collection-post", "Boulder:read"])]
    private ?Media $rockImage = null;

    #[ORM\ManyToOne(targetEntity: Boulder::class, inversedBy: "lineBoulders")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Groups(["LineBoulder:read", "LineBoulder:collection-post"])]
    private ?Boulder $boulder = null;

    #[Assert\NotBlank()]
    #[ORM\Column(type: 'text')]
    #[Groups(["LineBoulder:read", "LineBoulder:write", "Boulder:read"])]
    private string $smoothLine;

    /**
     * @var non-empty-array<int, non-empty-array<int, array{x: float, y: float}>>
     */
    #[Assert\Type('array')]
    #[Assert\NotBlank()]
    #[ORM\Column(type: 'json')]
    #[Groups(["LineBoulder:read", "LineBoulder:write"])]
    private array $arrArrPoints;

    #[Assert\Callback()]
    public function validate(ExecutionContextInterface $context): void
    {
        if (!$this->boulder || !$this->rockImage) {
            return;
        }

        if ($this->boulder->getRock() !== $this->rockImage->getRock()) {
            $context->buildViolation('This boulder does not match with its rock associated')
                ->atPath('boulder')
                ->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRockImage(): ?Media
    {
        return $this->rockImage;
    }

    public function setRockImage(?Media $rockImage): self
    {
        $this->rockImage = $rockImage;

        return $this;
    }

    public function getBoulder(): ?Boulder
    {
        return $this->boulder;
    }

    public function setBoulder(?Boulder $boulder): self
    {
        $this->boulder = $boulder;

        return $this;
    }

    public function getSmoothLine(): string
    {
        return $this->smoothLine;
    }

    public function setSmoothLine(string $smoothLine): self
    {
        $this->smoothLine = $smoothLine;

        return $this;
    }

    /**
     * @return non-empty-array<int, non-empty-array<int, array{x: float, y: float}>>
     */
    public function getArrArrPoints(): array
    {
        return $this->arrArrPoints;
    }

    /**
     * @param non-empty-array<int, non-empty-array<int, array{x: float, y: float}>> $arrArrPoints
     */
    public function setArrArrPoints(array $arrArrPoints): self
    {
        $this->arrArrPoints = $arrArrPoints;

        return $this;
    }
}
