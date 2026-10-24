<?php

namespace App\DataFixtures;

use App\Story\DefaultPlantsStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PlantFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        DefaultPlantsStory::load();
    }

    public function getDependencies(): array
    {
        return [
            LunarTypeFixtures::class,
            SpeciesFixtures::class,
            PartFixtures::class,
            SoilTypeFixtures::class,
        ];
    }
}
