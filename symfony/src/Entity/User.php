<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user_table')]
#[UniqueEntity('email')]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: "integer")]
    private ?int $id;

    #[ORM\Column(type: "string", length: 180, unique: true)]
    #[Assert\Email()]
    #[Assert\Length(max: 180)]
    #[Assert\NotBlank()]
    private ?string $email;

    /**
     * @var array<string> $roles
     */
    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $lastAuthenticatedAt;

    public function __toString()
    {
        return $this->email ?? 'User #' . $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): ?string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        sort($roles);

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getLastAuthenticatedAt(): ?\DateTimeInterface
    {
        return $this->lastAuthenticatedAt;
    }

    public function setLastAuthenticatedAt(?\DateTimeInterface $lastAuthenticatedAt): self
    {
        $this->lastAuthenticatedAt = $lastAuthenticatedAt;

        return $this;
    }
}
