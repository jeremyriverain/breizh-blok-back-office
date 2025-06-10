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
     * @var Collection<int, BoulderAttempt>
     */
    #[ORM\OneToMany(mappedBy: 'userInfo', targetEntity: BoulderAttempt::class, orphanRemoval: true)]
    private Collection $boulderAttempts;

    public function __construct()
    {
        $this->boulderAttempts = new ArrayCollection();
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
     * @return Collection<int, BoulderAttempt>
     */
    public function getBoulderAttempts(): Collection
    {
        return $this->boulderAttempts;
    }

    public function addBoulderAttempt(BoulderAttempt $boulderAttempt): static
    {
        if (!$this->boulderAttempts->contains($boulderAttempt)) {
            $this->boulderAttempts->add($boulderAttempt);
            $boulderAttempt->setUserInfo($this);
        }

        return $this;
    }

    public function removeBoulderAttempt(BoulderAttempt $boulderAttempt): static
    {
        if ($this->boulderAttempts->removeElement($boulderAttempt)) {
            // set the owning side to null (unless already changed)
            if ($boulderAttempt->getUserInfo() === $this) {
                $boulderAttempt->setUserInfo(null);
            }
        }

        return $this;
    }
}
