<?php

namespace App\DataFixtures;

use App\Entity\Land;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LandSettingFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $land4 = $this->getReference(LandFixtures::LAND_4, Land::class);
        $land4->getLandSetting()->setLookingForMember(true);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            LandFixtures::class,
        ];
    }
}
