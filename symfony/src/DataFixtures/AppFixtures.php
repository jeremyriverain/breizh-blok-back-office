<?php

namespace App\DataFixtures;

use App\DataFixtures\Story\DefaultBoulderAreasStory;
use App\DataFixtures\Story\DefaultBoulderFeedbacksStory;
use App\DataFixtures\Story\DefaultDepartmentsStory;
use App\DataFixtures\Story\DefaultHeightBouldersStory;
use App\DataFixtures\Story\DefaultLineBouldersStory;
use App\DataFixtures\Story\DefaultMunicipalitiesStory;
use App\DataFixtures\Story\DefaultRocksStory;
use App\DataFixtures\Story\DefaultUsersStory;
use App\DataFixtures\Story\DefaultGradesStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DefaultUsersStory::load();
        DefaultGradesStory::load();
        DefaultHeightBouldersStory::load();
        DefaultMunicipalitiesStory::load();
        DefaultDepartmentsStory::load();
        DefaultBoulderAreasStory::load();
        DefaultRocksStory::load();
        DefaultLineBouldersStory::load();
        DefaultBoulderFeedbacksStory::load();
    }
}
