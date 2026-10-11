<?php

namespace App\DataFixtures;

use App\Entity\Land;
use App\Entity\LandApiKey;
use App\Factory\LandApiKeyFactory;
use App\Security\Constant\LandMemberPermission;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LandApiKeyFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $landApiKey1 = LandApiKeyFactory::new()->create([
            'land' => $this->getReference(LandFixtures::LAND_1, Land::class),
            'permissions' => LandMemberPermission::ALL,
        ]);

        $this->outputToken($landApiKey1);

        $landApiKey2 = LandApiKeyFactory::new()->create([
            'land' => $this->getReference(LandFixtures::LAND_2, Land::class),
            'permissions' => LandMemberPermission::ALL,
        ]);

        $this->outputToken($landApiKey2);
    }

    private function outputToken(LandApiKey $apiKey): void
    {
        echo "-----LAND API KEY\n".$apiKey->getToken()."\n";
    }

    public function getDependencies(): array
    {
        return [
            LandFixtures::class,
        ];
    }
}
