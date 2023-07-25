<?php

namespace App\Interfaces;

interface ContainsMediaInterface
{
    /**
     * @return array<int, string>
     */
    public function getMediaAttributes(): array;
}
