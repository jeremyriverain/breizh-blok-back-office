<?php

namespace App\Interfaces;

interface IContainsMedia
{
    /**
     * @return array<int, string>
     */
    public function getMediaAttributes(): array;
}
