<?php

namespace App\Api\Resource\AreaRequest\Dto;

use App\Entity\AreaRequest;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The body of the `reject` transition: the reason shown to the owner, mapped onto the
 * entity before the transition is applied.
 */
#[Map(target: AreaRequest::class)]
final class AreaRequestReject
{
    #[Assert\NotBlank(normalizer: 'trim')]
    #[Assert\Length(max: 2000)]
    public string $rejectionReason;
}
