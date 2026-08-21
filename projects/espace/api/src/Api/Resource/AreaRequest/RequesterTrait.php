<?php

namespace App\Api\Resource\AreaRequest;

use App\Entity\Person;

trait RequesterTrait
{
    public ?string $requesterFirstName = null;
    public ?string $requesterLastName = null;

    public function setRequester(Person $requester): void
    {
        $this->requesterFirstName = $requester->getGivenName();
        $this->requesterLastName = $requester->getFamilyName();
    }
}
