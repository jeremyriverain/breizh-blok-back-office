<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Repository\BoulderFeedbackRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(security: "is_granted('ROLE_USING_TOKEN')")]
#[Post(security: "is_granted('ROLE_USING_TOKEN')")]
#[ORM\Entity(repositoryClass: BoulderFeedbackRepository::class)]
class BoulderFeedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?GeoPoint $newLocation = null;

    #[ORM\ManyToOne]
    private ?Grade $newGrade = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'boulderFeedbacks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserInfo $sentBy = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $receivedAt = null;

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

    public function getNewGrade(): ?Grade
    {
        return $this->newGrade;
    }

    public function setNewGrade(?Grade $newGrade): static
    {
        $this->newGrade = $newGrade;

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

    public function getSentBy(): ?UserInfo
    {
        return $this->sentBy;
    }

    public function setSentBy(?UserInfo $sentBy): static
    {
        $this->sentBy = $sentBy;

        return $this;
    }

    public function getReceivedAt(): ?\DateTimeImmutable
    {
        return $this->receivedAt;
    }

    public function setReceivedAt(\DateTimeImmutable $receivedAt): static
    {
        $this->receivedAt = $receivedAt;

        return $this;
    }
}
