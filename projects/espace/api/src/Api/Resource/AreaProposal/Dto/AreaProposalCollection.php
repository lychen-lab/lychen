<?php

namespace App\Api\Resource\AreaProposal\Dto;

use App\Api\Trait\PlaceTrait;
use App\Api\Trait\UuidIdentifierTrait;
use App\Entity\AreaActivity;
use App\Entity\AreaProposal;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[Map(source: AreaProposal::class)]
final class AreaProposalCollection
{
    use UuidIdentifierTrait;
    use PlaceTrait;

    public ?string $title;
    public ?string $description;
    public ?int $surfaceToShare;
    public ?string $city;
    public ?int $altitude;

    /**
     * @var string[]
     */
    public array $activities;

    /**
     * @param iterable<AreaActivity> $activities
     */
    public function setActivities(iterable $activities): void
    {
        $this->activities = [];
        foreach ($activities as $activity) {
            if ($code = $activity->getCode()) {
                $this->activities[] = $code;
            }
        }
    }
}
