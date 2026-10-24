<?php

namespace App\DataFixtures;

use App\Story\DefaultFamiliesStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class FamilyFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        DefaultFamiliesStory::load();
    }

    public static function getGroups(): array
    {
        return ['initial_data'];
    }
}
