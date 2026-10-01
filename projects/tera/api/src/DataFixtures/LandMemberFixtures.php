<?php

namespace App\DataFixtures;

use App\Entity\Land;
use App\Entity\LandRole;
use App\Entity\Person;
use App\Factory\LandMemberFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LandMemberFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        LandMemberFactory::new()->create([
            'land' => $this->getReference(LandFixtures::LAND_1, Land::class),
            'person' => $this->getReference(PersonFixtures::PERSON_4, Person::class),
            'owner' => false,
            'landRoles' => [$this->getReference(LandRoleFixtures::LAND_1_ROLE_COLLABORATOR, LandRole::class)],
        ]);

        LandMemberFactory::new()->create([
            'land' => $this->getReference(LandFixtures::LAND_1, Land::class),
            'person' => $this->getReference(PersonFixtures::PERSON_5, Person::class),
            'owner' => false,
            'landRoles' => [$this->getReference(LandRoleFixtures::LAND_1_ROLE_COLLABORATOR, LandRole::class)],
        ]);

        LandMemberFactory::new()->create([
            'land' => $this->getReference(LandFixtures::LAND_3, Land::class),
            'person' => $this->getReference(PersonFixtures::PERSON_1, Person::class),
            'owner' => false,
            'landRoles' => [$this->getReference(LandRoleFixtures::LAND_3_ROLE_COLLABORATOR, LandRole::class)],
        ]);
    }

    public function getDependencies(): array
    {
        return [
            LandFixtures::class,
            LandRoleFixtures::class,
            PersonFixtures::class,
        ];
    }
}
