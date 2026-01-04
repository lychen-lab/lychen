<?php

namespace App\DataFixtures;

use App\Story\DefaultAreaProposalsStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AreaProposalFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        DefaultAreaProposalsStory::load();
    }

    public function getDependencies(): array
    {
        return [
            AreaActivityFixtures::class
        ];
    }
}
