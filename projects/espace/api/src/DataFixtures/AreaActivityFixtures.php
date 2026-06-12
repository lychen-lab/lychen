<?php

namespace App\DataFixtures;

use App\Story\DefaultAreaActivitiesStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AreaActivityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DefaultAreaActivitiesStory::load();
    }
}
