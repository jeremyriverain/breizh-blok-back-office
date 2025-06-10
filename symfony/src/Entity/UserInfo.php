<?php

namespace App\Entity;

use App\Repository\UserInfoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserInfoRepository::class)]
class UserInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $identifier = null;

    /**
     * @var Collection<int, BoulderFeedback>
     */
    #[ORM\OneToMany(mappedBy: 'sentBy', targetEntity: BoulderFeedback::class, orphanRemoval: true)]
    private Collection $boulderFeedbacks;

    public function __construct()
    {
        $this->boulderFeedbacks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @return Collection<int, BoulderFeedback>
     */
    public function getBoulderFeedbacks(): Collection
    {
        return $this->boulderFeedbacks;
    }

    public function addBoulderFeedback(BoulderFeedback $boulderFeedback): static
    {
        if (!$this->boulderFeedbacks->contains($boulderFeedback)) {
            $this->boulderFeedbacks->add($boulderFeedback);
            $boulderFeedback->setSentBy($this);
        }

        return $this;
    }

    public function removeBoulderFeedback(BoulderFeedback $boulderFeedback): static
    {
        if ($this->boulderFeedbacks->removeElement($boulderFeedback)) {
            // set the owning side to null (unless already changed)
            if ($boulderFeedback->getSentBy() === $this) {
                $boulderFeedback->setSentBy(null);
            }
        }

        return $this;
    }
}
