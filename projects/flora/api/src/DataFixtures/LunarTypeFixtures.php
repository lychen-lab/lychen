<?php

namespace App\DataFixtures;

use App\Story\DefaultLunarTypesStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class LunarTypeFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        DefaultLunarTypesStory::load();
    }

    public static function getGroups(): array
    {
        return ['initial_data'];
    }
}
