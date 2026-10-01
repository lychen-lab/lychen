<?php

namespace App\DataFixtures;

use App\Factory\LandAreaFactory;
use App\Factory\LandFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LandAreaFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        LandAreaFactory::new(function () {
            return ['land' => LandFactory::random()];
        })->range(15, 25)->create();
    }

    public function getDependencies(): array
    {
        return [
            LandFixtures::class,
        ];
    }
}
