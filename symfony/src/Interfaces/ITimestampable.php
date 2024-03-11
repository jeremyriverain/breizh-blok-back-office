<?php

namespace App\Interfaces;

interface ITimestampable
{
    public function getCreatedAt(): ?\DateTimeInterface;
    public function setCreatedAt(\DateTimeInterface $dateTime): self;
}
