<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\BoulderFeedbackRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ApiResource(
    security: "is_granted('ROLE_USING_TOKEN')",
    normalizationContext: ['groups' => ['BoulderFeedback:read']],
    denormalizationContext: ['groups' => ['BoulderFeedback:write']],
)]
#[Post(security: "is_granted('ROLE_USING_TOKEN')")]
#[GetCollection(security: "is_granted('ROLE_USING_TOKEN')")]
#[Get(security: "is_granted('ROLE_USING_TOKEN') and object.getSentBy() == user.getUserIdentifier()")]
#[ORM\Entity(repositoryClass: BoulderFeedbackRepository::class)]
class BoulderFeedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(['BoulderFeedback:read', 'BoulderFeedback:write'])]
    #[Assert\Valid()]
    private ?GeoPoint $newLocation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['BoulderFeedback:read', 'BoulderFeedback:write'])]
    private ?string $message = null;

    #[ORM\Column(length: 255)]
    #[Groups(['BoulderFeedback:read'])]
    private ?string $sentBy = null;

    #[ORM\ManyToOne(inversedBy: 'feedbacks')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['BoulderFeedback:read', 'BoulderFeedback:write'])]
    #[Assert\NotBlank()]
    private ?Boulder $boulder = null;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['BoulderFeedback:read'])]
    private ?\DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->setCreatedAt(Carbon::now()->toImmutable());
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addConstraint(new Assert\Callback('validate'));
    }

    public function validate(ExecutionContextInterface $context): void
    {
        $hasNewLocation = null !== $this->getNewLocation();
        $hasMessage = !empty(trim($this->getMessage() ?? ''));

        if (!$hasNewLocation && !$hasMessage) {
            $context->buildViolation('atLeastOneFeedbackField')
                    ->atPath('message')
                    ->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNewLocation(): ?GeoPoint
    {
        return $this->newLocation;
    }

    public function setNewLocation(?GeoPoint $newLocation): static
    {
        $this->newLocation = $newLocation;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getSentBy(): ?string
    {
        return $this->sentBy;
    }

    public function setSentBy(string $sentBy): static
    {
        $this->sentBy = $sentBy;

        return $this;
    }

    public function getBoulder(): ?Boulder
    {
        return $this->boulder;
    }

    public function setBoulder(?Boulder $boulder): static
    {
        $this->boulder = $boulder;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $dateTime): self
    {
        $this->createdAt = $dateTime;

        return $this;
    }
}
