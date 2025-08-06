<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\HeightBoulderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

#[ORM\Entity(repositoryClass: HeightBoulderRepository::class)]
#[UniqueEntity(
    fields: ['min', 'max'],
    errorPath: 'min',
    message: 'theCombinationOfMinAndMaxAlreadyExist',
)]
#[ApiResource(
    normalizationContext: ['groups' => ['HeightBoulder:read']],
    operations: [
        new Get(),
        new GetCollection(),
    ],
    paginationClientEnabled: true
)]
class HeightBoulder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotBlank()]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\LessThan(15)]
    #[Groups(['Boulder:item-get'])]
    private ?int $min = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    #[Assert\LessThan(15)]
    #[Groups(['Boulder:item-get'])]
    private ?int $max = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public static function trans(TranslatorInterface $translator, HeightBoulder $heightBoulder): string
    {
        if (0 === $heightBoulder->getMin() && null !== $heightBoulder->getMax()) {
            return $translator->trans('heightLessThan', [
                '%value%' => $heightBoulder->getMax(),
            ]);
        }

        if (null !== $heightBoulder->getMax()) {
            return $translator->trans('heightBetween', [
                '%min%' => $heightBoulder->getMin(),
                '%max%' => $heightBoulder->getMax(),
            ]);
        }

        return $translator->trans('heightMoreThan', [
            '%value%' => $heightBoulder->getMin(),
        ]);
    }

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function setMin(int $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(?int $max): static
    {
        $this->max = $max;

        return $this;
    }
}
