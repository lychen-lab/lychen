<?php

namespace App\Api\Resource\AreaProposal\Dto;

use App\Entity\AreaProposal;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The body of the `reject` transition: the reason shown to the owner, mapped onto the
 * entity before the transition is applied.
 */
#[Map(target: AreaProposal::class)]
final class AreaProposalReject
{
    #[Assert\NotBlank(normalizer: 'trim')]
    #[Assert\Length(max: 2000)]
    public string $rejectionReason;
}
