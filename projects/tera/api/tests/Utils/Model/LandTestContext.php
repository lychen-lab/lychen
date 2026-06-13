<?php

namespace App\Tests\Utils\Model;

use App\Entity\Land;
use App\Entity\LandArea;
use App\Entity\LandCultivationPlan;
use App\Entity\LandGreenhouse;
use App\Entity\LandHarvestEntry;
use App\Entity\LandMember;
use App\Entity\LandMemberInvitation;
use App\Entity\LandRole;
use App\Entity\LandTask;
use App\Entity\Person;

class LandTestContext
{
    public Person $owner;
    public Land $land;

    /** @var LandArea[] */
    public array $landAreas;

    /** @var LandMember[] */
    public array $landMembers;

    /** @var LandGreenhouse[] */
    public array $landGreenhouses;

    /** @var LandCultivationPlan[] */
    public array $landCultivationPlans;

    /** @var LandTask[] */
    public array $landTasks;

    /** @var LandRole[] */
    public array $landRoles;

    /** @var LandMemberInvitation[] */
    public array $landMemberInvitations;

    /** @var LandHarvestEntry[] */
    public array $landHarvestEntries;

    public function setOwner(Person $owner): static
    {
        $this->owner = $owner;
        return $this;
    }

    public function setLand(Land $land): static
    {
        $this->land = $land;
        return $this;
    }

    public function addLandArea(LandArea $landArea): static
    {
        $this->landAreas[] = $landArea;
        return $this;
    }

    public function addLandMember(LandMember $landMember): static
    {
        $this->landMembers[] = $landMember;
        return $this;
    }

    public function addLandGreenhouse(LandGreenhouse $landGreenhouse): static
    {
        $this->landGreenhouses[] = $landGreenhouse;
        return $this;
    }

    public function addLandCultivationPlan(LandCultivationPlan $landCultivationPlan): static
    {
        $this->landCultivationPlans[] = $landCultivationPlan;
        return $this;
    }

    public function addLandTask(LandTask $landTask): static
    {
        $this->landTasks[] = $landTask;
        return $this;
    }

    public function addLandRole(LandRole $landRole): static
    {
        $this->landRoles[] = $landRole;
        return $this;
    }

    public function addLandMemberInvitation(LandMemberInvitation $landMemberInvitation): static
    {
        $this->landMemberInvitations[] = $landMemberInvitation;
        return $this;
    }

    public function addLandHarvestEntry(LandHarvestEntry $landHarvestEntry): static
    {
        $this->landHarvestEntries[] = $landHarvestEntry;
        return $this;
    }
}
