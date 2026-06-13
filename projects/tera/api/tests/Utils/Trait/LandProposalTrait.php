<?php

namespace App\Tests\Utils\Trait;

use App\Entity\Land;
use App\Entity\LandProposal;
use App\Factory\LandProposalFactory;

trait LandProposalTrait
{
    protected function createLandProposal(Land $land, ?array $attributes = null): LandProposal
    {
        return LandProposalFactory::new()->create(array_merge([
            'land' => $land,
        ], $attributes ?? []));
    }
}
