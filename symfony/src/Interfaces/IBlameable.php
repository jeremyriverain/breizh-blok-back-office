<?php

namespace App\Interfaces;

use App\Entity\User;

interface IBlameable
{
    public function getCreatedBy(): ?User;
    public function setCreatedBy(?User $user): self;
}
