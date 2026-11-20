<?php

namespace App\Security\Voter;

use App\Security\Interface\LandAwareInterface;

interface LandAwareVoterInterface
{
    /**
     * @return class-string<LandAwareInterface>
     */
    public function getSupportedClass(): string;

    /**
     * @return list<string>
     */
    public function getAvailablePermissions(): array;
}
