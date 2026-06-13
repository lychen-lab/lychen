<?php

namespace App\DataFixtures;

use App\Entity\Land;
use App\Factory\LandRoleFactory;
use App\Security\Constant\LandMemberPermission;
use App\Security\Voter\LandSettingVoter;
use App\Security\Voter\LandVoter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LandRoleFixtures extends Fixture implements DependentFixtureInterface
{
    public const string LAND_1_ROLE_ADMIN = 'land-one-role-administrateur';
    public const string LAND_1_ROLE_COLLABORATOR = 'land-one-role-collaborator';
    public const string LAND_2_ROLE_ADMIN = 'land-two-role-admin';
    public const string LAND_2_ROLE_MANAGER = 'land-two-role-manager';
    public const string LAND_3_ROLE_COLLABORATOR = 'land-three-role-collaborator';
    public const string LAND_4_ROLE_COLLABORATOR = 'land-four-role-collaborator';

    public function load(ObjectManager $manager): void
    {
        $this->createLandRoleAndAddReference(self::LAND_1_ROLE_ADMIN, [
            'land' => $this->getReference(LandFixtures::LAND_1, Land::class),
            'name' => 'Administrateur L1',
            'permissions' => LandMemberPermission::ALL,
        ]);

        $this->createLandRoleAndAddReference(self::LAND_1_ROLE_COLLABORATOR, [
            'land' => $this->getReference(LandFixtures::LAND_1, Land::class),
            'name' => 'Collaborateur L1',
            'permissions' => LandMemberPermission::ALL,
        ]);

        $this->createLandRoleAndAddReference(self::LAND_2_ROLE_ADMIN, [
            'land' => $this->getReference(LandFixtures::LAND_2, Land::class),
            'name' => 'Admin L2',
            'permissions' => LandMemberPermission::ALL,
        ]);

        $this->createLandRoleAndAddReference(self::LAND_2_ROLE_MANAGER, [
            'land' => $this->getReference(LandFixtures::LAND_2, Land::class),
            'name' => 'Manager L2',
            'permissions' => LandMemberPermission::ALL,
        ]);

        $this->createLandRoleAndAddReference(self::LAND_4_ROLE_COLLABORATOR, [
            'land' => $this->getReference(LandFixtures::LAND_4, Land::class),
            'name' => 'Collaborator L4',
            'permissions' => array_diff(LandMemberPermission::ALL, LandSettingVoter::ALL,
                [LandVoter::PATCH, LandVoter::DELETE]),
        ]);

        $this->createLandRoleAndAddReference(self::LAND_3_ROLE_COLLABORATOR, [
            'land' => $this->getReference(LandFixtures::LAND_3, Land::class),
            'name' => 'Collaborator L3',
            'permissions' => LandMemberPermission::ALL,
        ]);
    }

    private function createLandRoleAndAddReference(string         $reference,
                                                   array|callable $attributes = []): void
    {
        $landRole = LandRoleFactory::new()->create($attributes);
        $this->addReference($reference, $landRole);
    }

    public function getDependencies(): array
    {
        return [
            PersonFixtures::class
        ];
    }
}
