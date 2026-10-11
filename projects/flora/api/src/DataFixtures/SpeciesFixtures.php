<?php

namespace App\DataFixtures;

use App\Story\DefaultSpeciesStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SpeciesFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        DefaultSpeciesStory::load();
    }

    public function getDependencies(): array
    {
        return [
            FamilyFixtures::class,
        ];
    }

    public static function getGroups(): array
    {
        return ['initial_data'];
    }
}
