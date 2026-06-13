<?php

namespace App\DataFixtures;

use App\Entity\Person;
use App\Factory\LandRequestFactory;
use App\Workflow\LandRequest\LandRequestWorkflowTransition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Workflow\WorkflowInterface;
use function Zenstruck\Foundry\Persistence\save;

class LandRequestFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly WorkflowInterface $landRequestStateMachine)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $landRequest4 = LandRequestFactory::new()->create([
            'person' => $this->getReference(PersonFixtures::PERSON_4, Person::class)
        ]);
        $this->landRequestStateMachine->apply($landRequest4, LandRequestWorkflowTransition::PUBLISH);

        $landRequest5 = LandRequestFactory::new()->create([
            'person' => $this->getReference(PersonFixtures::PERSON_5, Person::class)
        ]);
        $this->landRequestStateMachine->apply($landRequest5, LandRequestWorkflowTransition::PUBLISH);

        $landRequest6 = LandRequestFactory::new()->create([
            'person' => $this->getReference(PersonFixtures::PERSON_6, Person::class)
        ]);
        $this->landRequestStateMachine->apply($landRequest6, LandRequestWorkflowTransition::PUBLISH);

        $landRequest7 = LandRequestFactory::new()->create([
            'person' => $this->getReference(PersonFixtures::PERSON_7, Person::class)
        ]);
        $this->landRequestStateMachine->apply($landRequest7, LandRequestWorkflowTransition::PUBLISH);
        save($landRequest7);
    }


    public function getDependencies(): array
    {
        return [
            PersonFixtures::class,
        ];
    }
}
