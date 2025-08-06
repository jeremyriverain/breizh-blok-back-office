<?php

namespace App\Interfaces;

interface IUpdatable
{
    public function getUpdatedAt(): ?\DateTimeInterface;

    public function setUpdatedAt(\DateTimeInterface $dateTime): self;
}
