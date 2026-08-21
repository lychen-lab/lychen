<?php

namespace App\DataFixtures;

use App\Story\DefaultAreaRequestsStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AreaRequestFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        DefaultAreaRequestsStory::load();
    }

    public function getDependencies(): array
    {
        return [
            AreaActivityFixtures::class,
            PersonFixtures::class,
        ];
    }
}
