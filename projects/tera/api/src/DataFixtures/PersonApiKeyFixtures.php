<?php

namespace App\DataFixtures;

use App\Entity\Person;
use App\Entity\PersonApiKey;
use App\Factory\PersonApiKeyFactory;
use App\Security\Constant\PersonPermission;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PersonApiKeyFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $personApiKey1 = PersonApiKeyFactory::new()->create([
            'person' => $this->getReference(PersonFixtures::PERSON_1, Person::class),
            'permissions' => PersonPermission::ALL,
        ]);

        $this->outputToken($personApiKey1);

        $personApiKey2 = PersonApiKeyFactory::new()->create([
            'person' => $this->getReference(PersonFixtures::PERSON_2, Person::class),
            'permissions' => PersonPermission::ALL,
        ]);

        $this->outputToken($personApiKey2);
    }

    private function outputToken(PersonApiKey $apiKey): void
    {
        echo "-----PERSON API KEY\n".$apiKey->getToken()."\n";
    }

    public function getDependencies(): array
    {
        return [
            PersonFixtures::class,
        ];
    }
}
