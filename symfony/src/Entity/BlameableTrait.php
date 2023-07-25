<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

trait BlameableTrait {

    #[Gedmo\Blameable(on: "create")]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    private ?User $createdBy;

    #[Gedmo\Blameable(on: "update")]
    #[ORM\ManyToOne()]
    #[ORM\JoinColumn(onDelete: "SET NULL")]
    private ?User $updatedBy = null;

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

}