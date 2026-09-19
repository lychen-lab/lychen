<?php

namespace App\Api\Resource\AreaRequest\Dto;

use App\Api\Transform\ActivityCodesToActivities;
use App\Entity\AreaRequest;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\ObjectMapper\Condition\IsNotNull;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Properties are deliberately left uninitialized: only the fields sent by the client
 * get initialized and the object mapper skips the others, which gives merge-patch
 * semantics. The non-nullable types reject explicit nulls. $activities is the
 * exception: the mapper reads properties carrying a #[Map] before checking they are
 * initialized, so it defaults to null and is skipped while null.
 */
#[Map(target: AreaRequest::class)]
final class AreaRequestPatch
{
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    public string $title;

    #[Assert\NotBlank(allowNull: true)]
    public string $description;

    #[Assert\Positive]
    public int $minimalSurfaceRequested;

    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    public string $city;

    /**
     * @var list<string>|null
     */
    #[Map(if: new IsNotNull(), transform: ActivityCodesToActivities::class)]
    public ?array $activities = null;
}
