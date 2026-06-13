<?php

namespace App\DataFixtures;

use App\Entity\Person;
use App\Factory\LandFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LandFixtures extends Fixture implements DependentFixtureInterface
{
    public const string LAND_1 = 'land-one';
    public const string LAND_2 = 'land-two';
    public const string LAND_3 = 'land-three';
    public const string LAND_4 = 'land-four';
    public const string LAND_5 = 'land-five';
    public const string LAND_6 = 'land-six';
    public const string LAND_7 = 'land-seven';

    public function load(ObjectManager $manager): void
    {
        $this->createLandAndAddReference(self::LAND_1,
            ['name' => 'Champ Du Clos', 'owner' => $this->getReference(PersonFixtures::PERSON_1, Person::class)]);
        $this->createLandAndAddReference(self::LAND_2,
            ['name' => 'Terrain 3', 'owner' => $this->getReference(PersonFixtures::PERSON_2, Person::class)]);
        $this->createLandAndAddReference(self::LAND_3,
            ['name' => 'Amazing Forest', 'owner' => $this->getReference(PersonFixtures::PERSON_3, Person::class)]);
        $this->createLandAndAddReference(self::LAND_4,
            ['name' => 'Yupo Garden', 'owner' => $this->getReference(PersonFixtures::PERSON_4, Person::class)]);
        $this->createLandAndAddReference(self::LAND_5,
            ['name' => 'Le clos du Marucher', 'owner' => $this->getReference(PersonFixtures::PERSON_5, Person::class)]);
        $this->createLandAndAddReference(self::LAND_6,
            ['name' => 'La Tanière Alpine', 'owner' => $this->getReference(PersonFixtures::PERSON_6, Person::class)]);
        $this->createLandAndAddReference(self::LAND_7,
            ['name' => 'Champ du bas', 'owner' => $this->getReference(PersonFixtures::PERSON_7, Person::class)]);
    }

    private function createLandAndAddReference(string $reference, array|callable $attributes = []): void
    {
        $land = LandFactory::new()->create($attributes);
        $this->addReference($reference, $land);
    }

    public function getDependencies(): array
    {
        return [
            PersonFixtures::class
        ];
    }
}
