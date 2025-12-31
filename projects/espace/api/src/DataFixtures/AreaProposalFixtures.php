<?php

namespace App\DataFixtures;

use App\Story\DefaultAreaProposalsStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AreaProposalFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DefaultAreaProposalsStory::load();
    }
}
