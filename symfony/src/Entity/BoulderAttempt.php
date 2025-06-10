<?php

namespace App\Entity;

use App\Repository\BoulderAttemptRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoulderAttemptRepository::class)]
class BoulderAttempt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Boulder $boulder = null;

    #[ORM\ManyToOne(inversedBy: 'boulderAttempts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserInfo $userInfo = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserInfo(): ?UserInfo
    {
        return $this->userInfo;
    }

    public function setUserInfo(?UserInfo $userInfo): static
    {
        $this->userInfo = $userInfo;

        return $this;
    }
}
