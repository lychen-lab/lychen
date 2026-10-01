<?php

namespace App\DataFixtures;

use App\Entity\Land;
use App\Entity\LandRole;
use App\Factory\LandMemberInvitationFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LandMemberInvitationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        LandMemberInvitationFactory::new()->create([
            'land' => $this->getReference(LandFixtures::LAND_1, Land::class),
            'email' => PersonFixtures::buildUserEmail(PersonFixtures::PERSON_6),
            'landRoles' => [$this->getReference(LandRoleFixtures::LAND_1_ROLE_COLLABORATOR, LandRole::class)],
        ]);

        LandMemberInvitationFactory::new()->create([
            'land' => $this->getReference(LandFixtures::LAND_4, Land::class),
            'email' => PersonFixtures::buildUserEmail(PersonFixtures::PERSON_1),
            'landRoles' => [$this->getReference(LandRoleFixtures::LAND_4_ROLE_COLLABORATOR, LandRole::class)],
        ]);
    }

    public function getDependencies(): array
    {
        return [
            LandFixtures::class,
            PersonFixtures::class,
            LandRoleFixtures::class,
        ];
    }
}
