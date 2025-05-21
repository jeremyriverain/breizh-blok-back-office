<?php

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\HeightBoulderFactory;
use Zenstruck\Foundry\Story;

final class DefaultHeightBouldersStory extends Story
{
    public function build(): void
    {
        HeightBoulderFactory::createOne([
            'min' => 0,
            'max' => 3,
        ]);

        HeightBoulderFactory::createOne([
            'min' => 3,
            'max' => 5,
        ]);

        HeightBoulderFactory::createOne([
            'min' => 5,
            'max' => 5,
        ]);
    }
}
