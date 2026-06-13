<?php

namespace App\Tests\Utils\Trait;

use App\Entity\Person;
use App\Tests\Utils\Model\LandTestContext;

trait ContextTrait
{
    protected function createLandContext(): LandTestContext
    {
        $owner = $this->createPerson();
        $land = $this->createLand($owner);

        return (new LandTestContext())->setLand($land)->setOwner($owner);
    }

    protected function addOneLandArea(LandTestContext $landTestContext): LandTestContext
    {
        $landArea = $this->createLandArea($landTestContext->land);

        $landTestContext->addLandArea($landArea);

        return $landTestContext;
    }

    protected function addOneLandGreenhouse(LandTestContext $landTestContext): LandTestContext
    {
        $landGreenhouse = $this->createLandGreenhouse($landTestContext->land);

        $landTestContext->addLandGreenhouse($landGreenhouse);

        return $landTestContext;
    }

    protected function addOneLandCultivationPlan(LandTestContext $landTestContext): LandTestContext
    {
        $landCultivationPlan = $this->createLandCultivationPlan($landTestContext->land);

        $landTestContext->addLandCultivationPlan($landCultivationPlan);

        return $landTestContext;
    }

    protected function addOneLandTask(LandTestContext $landTestContext, ?array $attributes = []): LandTestContext
    {
        $landTask = $this->createLandTask($landTestContext->land, $attributes);

        $landTestContext->addLandTask($landTask);

        return $landTestContext;
    }

    protected function addOneLandRole(LandTestContext $landTestContext, ?array $permissions = null): LandTestContext
    {
        $landRole = $this->createLandRole($landTestContext->land, $permissions);

        $landTestContext->addLandRole($landRole);

        return $landTestContext;
    }

    protected function addLandMember(LandTestContext $landTestContext, ?array $roles = null, Person|null $person = null): LandTestContext
    {
        if (!$person) {
            $person = $this->createPerson();
        }
        $landMember = $this->createLandMember($landTestContext->land, $person, $roles);
        $landTestContext->addLandMember($landMember);

        return $landTestContext;
    }

    protected function addOneLandMemberInvitation(LandTestContext $landTestContext, ?array $roles = null, ?string $email = null): LandTestContext
    {
        $landMemberInvitation = $this->createLandMemberInvitation($landTestContext->land, $email, $roles);
        $landTestContext->addLandMemberInvitation($landMemberInvitation);

        return $landTestContext;
    }

    protected function addOneLandHarvestEntry(LandTestContext $landTestContext, ?array $attributes = []): LandTestContext
    {
        $landHarvestEntry = $this->createLandHarvestEntry($landTestContext->land, $attributes);

        $landTestContext->addLandHarvestEntry($landHarvestEntry);

        return $landTestContext;
    }
}
