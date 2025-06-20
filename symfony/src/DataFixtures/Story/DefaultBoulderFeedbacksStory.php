<?php

namespace App\DataFixtures\Story;

use App\DataFixtures\Factory\BoulderFeedbackFactory;
use App\Entity\Boulder;
use App\Entity\GeoPoint;
use Zenstruck\Foundry\Story;

use function Zenstruck\Foundry\Persistence\repository;

final class DefaultBoulderFeedbacksStory extends Story
{
    public function build(): void
    {
        $boulderRepository = repository(Boulder::class);

        BoulderFeedbackFactory::createOne([
            'boulder' => $boulderRepository->findOneBy(
                ['name' => 'Stone']
            ),
            'newLocation' => new GeoPoint(latitude: '45', longitude: '54'),
            'sentBy' => 'foo',
        ]);

        BoulderFeedbackFactory::createOne([
            'boulder' => $boulderRepository->findOneBy(
                ['name' => 'Monkey']
            ),
            'message' => 'I disagree with the current grade.',
            'sentBy' => 'bar',
        ]);
    }
}
