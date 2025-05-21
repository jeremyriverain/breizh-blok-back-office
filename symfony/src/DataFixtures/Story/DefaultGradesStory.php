<?php

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\GradeFactory;
use Zenstruck\Foundry\Story;

final class DefaultGradesStory extends Story
{
    public function build(): void
    {
        $grades = ['4', '5', '5+', '6a', '6a+', '6b', '6b+', '6c', '6c+', '7a', '7a+', '7b', '7b+', '7c', '7c+', '8a', '8a+', '8b', '8b+', '8c', '8c+', '9a'];

        GradeFactory::createSequence(
            function() use ($grades) {
                foreach ($grades as $grade) {
                    yield ['name' => $grade];
                }
            }
        );
    }
}
