<?php

namespace App\Interfaces;

interface ITimestampable
{
    public function getCreatedAt(): ?\DateTimeInterface;
    public function setCreatedAt(\DateTimeInterface $dateTime): self;
    public function getUpdatedAt(): ?\DateTimeInterface;
    public function setUpdatedAt(\DateTimeInterface $dateTime): self;
}
