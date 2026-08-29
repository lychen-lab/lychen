<?php

namespace App\Api\Resource\AreaProposal;

use App\Entity\Person;

trait ProposerTrait
{
    public ?string $proposerFirstName = null;
    public ?string $proposerLastName = null;

    public function setProposer(Person $proposer): void
    {
        $this->proposerFirstName = $proposer->getGivenName();
        $this->proposerLastName = $proposer->getFamilyName();
    }
}
