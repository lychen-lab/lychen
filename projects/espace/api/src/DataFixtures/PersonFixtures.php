<?php

namespace App\DataFixtures;

use App\Entity\Person;
use App\Factory\PersonFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Lychen\UtilZitadelBundle\Services\User;
use Symfony\Component\HttpClient\Exception\ClientException;

use function Zenstruck\Foundry\faker;

class PersonFixtures extends Fixture
{
    public const string DEFAULT_PASSWORD = '222|UG?64YjkiT7£';
    public const string DEFAULT_EMAIL_DOMAIN = '@fake.lychen.org';

    public const string PERSON_1 = 'person-1';
    public const string PERSON_2 = 'person-2';
    public const string PERSON_3 = 'person-3';
    public const string PERSON_4 = 'person-4';
    public const string PERSON_5 = 'person-5';
    public const string PERSON_6 = 'person-6';
    public const string PERSON_7 = 'person-7';
    public const string ADMIN_1 = 'admin-1';

    public function __construct(private readonly User $user)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->createUserOnZitadel(self::PERSON_1);
        $this->createUserOnZitadel(self::PERSON_2);
        $this->createUserOnZitadel(self::PERSON_3);
        $this->createUserOnZitadel(self::PERSON_4);
        $this->createUserOnZitadel(self::PERSON_5);
        $this->createUserOnZitadel(self::PERSON_6);
        $this->createUserOnZitadel(self::PERSON_7);
    }

    private function createUserOnZitadel(string $reference): Person
    {
        $userEmail = $this->buildUserEmail($reference);

        $givenName = faker()->firstName();
        $familyName = faker()->lastName();

        try {
            $data = $this->user->createHumanUser([
                'profile' => [
                    'givenName' => $givenName,
                    'familyName' => $familyName,
                ],
                'email' => [
                    'email' => $userEmail,
                    'isVerified' => true,
                ],
                'password' => [
                    'password' => self::DEFAULT_PASSWORD,
                    'changeRequired' => false,
                ],
            ]);
        } catch (ClientException $exception) {
            if (409 !== $exception->getResponse()->getStatusCode()) {
                throw $exception;
            }

            // The user already exists on Zitadel: reuse its identity and profile.
            $data = $this->user->searchByEmail($userEmail);
            $profile = $data['human']['profile'] ?? [];
            $givenName = $profile['givenName'] ?? $givenName;
            $familyName = $profile['familyName'] ?? $familyName;
        }

        return $this->createPersonAndAddReference($reference, [
            'authId' => $data['userId'],
            'email' => $userEmail,
            'givenName' => $givenName,
            'familyName' => $familyName,
        ]);
    }

    public static function buildUserEmail(string $reference): string
    {
        return $reference.self::DEFAULT_EMAIL_DOMAIN;
    }

    /**
     * @param array<string, mixed>|callable $attributes
     */
    private function createPersonAndAddReference(string $reference, array|callable $attributes = []): Person
    {
        $person = PersonFactory::new()->create($attributes);
        $this->addReference($reference, $person);

        return $person;
    }
}
