<?php

namespace App\DataFixtures;

use App\Entity\Land;
use App\Factory\LandProposalFactory;
use App\Workflow\LandProposal\LandProposalWorkflowTransition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Workflow\WorkflowInterface;
use function Zenstruck\Foundry\Persistence\save;

class LandProposalFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly WorkflowInterface $landProposalStateMachine)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $landProposal1 = LandProposalFactory::new()->create([
            'land' => $this->getReference(LandFixtures::LAND_1, Land::class)
        ]);
        $this->landProposalStateMachine->apply($landProposal1, LandProposalWorkflowTransition::PUBLISH);

        $landProposal2 = LandProposalFactory::new()->create([
            'land' => $this->getReference(LandFixtures::LAND_2, Land::class)
        ]);
        $this->landProposalStateMachine->apply($landProposal2, LandProposalWorkflowTransition::PUBLISH);

        $landProposal3 = LandProposalFactory::new()->create([
            'land' => $this->getReference(LandFixtures::LAND_3, Land::class)
        ]);
        $this->landProposalStateMachine->apply($landProposal3, LandProposalWorkflowTransition::PUBLISH);

        $landProposal4 = LandProposalFactory::new()->create([
            'land' => $this->getReference(LandFixtures::LAND_4, Land::class)
        ]);
        $this->landProposalStateMachine->apply($landProposal4, LandProposalWorkflowTransition::PUBLISH);

        $landProposal5 = LandProposalFactory::new()->create([
            'land' => $this->getReference(LandFixtures::LAND_5, Land::class)
        ]);
        $this->landProposalStateMachine->apply($landProposal5, LandProposalWorkflowTransition::PUBLISH);

        $landProposal6 = LandProposalFactory::new()->create([
            'land' => $this->getReference(LandFixtures::LAND_6, Land::class)
        ]);
        $this->landProposalStateMachine->apply($landProposal6, LandProposalWorkflowTransition::PUBLISH);

        $landProposal7 = LandProposalFactory::new()->create([
            'land' => $this->getReference(LandFixtures::LAND_7, Land::class)
        ]);
        $this->landProposalStateMachine->apply($landProposal7, LandProposalWorkflowTransition::PUBLISH);
        save($landProposal7);
    }


    public function getDependencies(): array
    {
        return [
            LandFixtures::class,
        ];
    }
}
