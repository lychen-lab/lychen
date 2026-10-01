<?php

namespace App\DataFixtures;

use App\Factory\LandFactory;
use App\Factory\LandGreenhouseFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LandGreenhouseFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        LandGreenhouseFactory::new(function () {
            return ['land' => LandFactory::random()];
        })->many(4)->create();
    }

    public function getDependencies(): array
    {
        return [
            LandFixtures::class,
        ];
    }
}
