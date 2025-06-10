<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\BoulderAttemptRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(security: "is_granted('ROLE_USING_TOKEN')")]
#[Get(security: "is_granted('ROLE_USING_TOKEN') or object.userInfo.identifier == user.userIdentifier")]
#[GetCollection(security: "is_granted('ROLE_USING_TOKEN')")]
#[Post(security: "is_granted('ROLE_USING_TOKEN')")]
#[ORM\Entity(repositoryClass: BoulderAttemptRepository::class)]
class BoulderAttempt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["BoulderAttempt:read"])]
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
