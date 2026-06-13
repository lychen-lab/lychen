<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\OpenApi\Model\Parameter;
use App\Constant\GardeningLevel;
use App\Constant\LandInteractionMode;
use App\Constant\LandSharingCondition;
use App\Constant\Orientation;
use App\Constant\SoilType;
use App\Entity\Interface\StateLandInterface;
use App\Filter\JsonbContainsFilter;
use App\Filter\LandFilter;
use App\Processor\WorkflowTransitionProcessor;
use App\Repository\LandProposalRepository;
use App\Security\Interface\LandAwareInterface;
use App\Security\Voter\LandProposalVoter;
use App\Validator\LandProposalOnlyOneStatePerLand;
use App\Workflow\LandProposal\LandProposalWorkflowPlace;
use App\Workflow\LandProposal\LandProposalWorkflowTransition;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LandProposalRepository::class)]
#[ApiResource]
#[GetCollection(
    normalizationContext: ['groups' => ['land_proposal:collection']],
    security            : "is_granted('" . LandProposalVoter::COLLECTION . "')",
    parameters          : [
        'order[:property]' => new QueryParameter(
            filter: 'land_proposal.order_filter'
        ),
        new QueryParameter(
            key   : 'land',
            filter: LandFilter::class
        ),
        new QueryParameter(
            key    : 'state',
            schema : [
                'type' => 'string',
                'enum' => LandProposalWorkflowPlace::PLACES,
                'example' => LandProposalWorkflowPlace::ARCHIVED,
            ],
            openApi: new Parameter(
                name           : 'state',
                in             : 'query',
                description    : 'Filter by state',
                required       : false,
                allowEmptyValue: true
            ),
            filter : 'common.collection_state_filter',
        ),
    ]
)]
#[GetCollection(
    uriTemplate         : '/land_proposals/public',
    normalizationContext: ['groups' => ['land_proposal:collection-public']],
    security            : "is_granted('" . LandProposalVoter::COLLECTION_PUBLIC . "')",
    name                : 'land-proposal_collection-public',
    parameters          : [
        'order[:property]' => new QueryParameter(
            filter: 'land_proposal.order_filter'
        ),
        new QueryParameter(
            key    : 'preferredInteractionMode',
            openApi: new Parameter(
                name           : 'preferredInteractionMode',
                in             : 'query',
                description    : 'Filter by preferred interaction mode',
                required       : false,
                allowEmptyValue: true
            ),
            filter : 'land_proposal.preferred_interaction_mode_filter'
        ),
        new QueryParameter(
            key    : 'sharingConditions',
            openApi: new Parameter(
                name           : 'sharingConditions',
                in             : 'query',
                description    : 'Filter by sharing conditions',
                required       : false,
                allowEmptyValue: true
            ),
            filter : JsonbContainsFilter::class
        ),
    ]
)]
#[Get(
    normalizationContext: ['groups' => ['land_proposal:get']],
    security            : "is_granted('" . LandProposalVoter::GET . "', object)"
)]
#[Post(
    normalizationContext   : ['groups' => ['land_proposal:post', 'land_proposal:post:output']],
    denormalizationContext : ['groups' => ['land_proposal:post', 'land_proposal:post:input']],
    securityPostDenormalize: "is_granted('" . LandProposalVoter::POST . "', object)"
)]
#[Patch(
    normalizationContext  : ['groups' => ['land_proposal:patch', 'land_proposal:patch:output']],
    denormalizationContext: ['groups' => ['land_proposal:patch', 'land_proposal:patch:input']],
    security              : "is_granted('" . LandProposalVoter::PATCH . "', previous_object)"
)]
#[Patch(
    uriTemplate           : '/land_proposals/{ulid}/' . LandProposalWorkflowTransition::PUBLISH,
    options               : ['transition' => LandProposalWorkflowTransition::PUBLISH],
    normalizationContext  : ['groups' => ['land_proposal:publish', 'land_proposal:publish:output']],
    denormalizationContext: ['groups' => ['land_proposal:publish', 'land_proposal:publish:input']],
    security              : "is_granted('" . LandProposalVoter::PUBLISH . "', previous_object)",
    processor             : WorkflowTransitionProcessor::class
)]
#[Patch(
    uriTemplate           : '/land_proposals/{ulid}/' . LandProposalWorkflowTransition::ARCHIVE,
    options               : ['transition' => LandProposalWorkflowTransition::ARCHIVE],
    normalizationContext  : ['groups' => ['land_proposal:archive', 'land_proposal:archive:output']],
    denormalizationContext: ['groups' => ['land_proposal:archive', 'land_proposal:archive:input']],
    security              : "is_granted('" . LandProposalVoter::ARCHIVE . "', previous_object)",
    processor             : WorkflowTransitionProcessor::class
)]
#[Delete(
    security: "is_granted('" . LandProposalVoter::DELETE . "', object)"
)]
#[ORM\HasLifecycleCallbacks]
#[LandProposalOnlyOneStatePerLand(states: [LandProposalWorkflowPlace::DRAFT, LandProposalWorkflowPlace::PUBLISHED])]
class LandProposal extends AbstractIdOrmAndUlidApiIdentified implements StateLandInterface, LandAwareInterface
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Column(length: 255)]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    private ?string $title = null;

    /**
     * @var array|null Tiptap JSON Object
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    private ?array $description = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: SoilType::ALL)]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    #[ApiProperty(openapiContext: [
        'type' => 'string',
        'enum' => SoilType::ALL,
        'example' => SoilType::HUMUS_RICH
    ])]
    private ?string $soilType = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(choices: Orientation::ALL)]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    #[ApiProperty(openapiContext: [
        'type' => 'string',
        'enum' => Orientation::ALL,
        'example' => Orientation::SOUTH_WEST
    ])]
    private ?string $orientation = null;

    #[ORM\Column]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    private ?bool $hasParking = false;

    #[ORM\Column]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    private ?bool $hasTools = false;

    #[ORM\Column]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    private ?bool $hasShed = false;

    #[ORM\Column]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    private ?bool $hasWaterPoint = false;

    #[ORM\Column]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    private ?bool $hasIndependentAccess = false;

    #[ORM\Column(length: 255)]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    private ?string $gardenState = null;

    #[ORM\Column(length: 30)]
    #[Assert\Choice(choices: LandInteractionMode::ALL)]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    #[ApiProperty(openapiContext: [
        'type' => 'array',
        'enum' => LandInteractionMode::ALL,
        'example' => LandInteractionMode::ALL
    ])]
    private ?string $preferredInteractionMode = LandInteractionMode::NO_PREFERENCE;

    #[ORM\Column(length: 30)]
    #[Assert\Choice(choices: GardeningLevel::ALL)]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    #[ApiProperty(openapiContext: [
        'type' => 'string',
        'enum' => GardeningLevel::ALL,
        'example' => GardeningLevel::BEGINNER
    ])]
    private ?string $gardeningLevel = GardeningLevel::BEGINNER;

    #[ORM\Column(length: 30)]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    #[Assert\Choice(choices: GardeningLevel::ALL)]
    #[ApiProperty(openapiContext: [
        'type' => 'string',
        'enum' => GardeningLevel::ALL,
        'example' => GardeningLevel::BEGINNER
    ])]
    private ?string $lookingForGardenerLevel = null;

    #[ORM\Column]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    private ?int $gardenTotalSurface = null;

    #[ORM\Column]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    private ?bool $foodSecurityParticipation = false;

    #[ORM\ManyToOne(inversedBy: 'landProposals')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch:output"])]
    private ?Land $land = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: LandProposalWorkflowPlace::PLACES)]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post:output",
              "land_proposal:patch:output",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    private ?string $state = LandProposalWorkflowPlace::DRAFT;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['jsonb' => true])]
    #[Assert\Choice(choices: LandSharingCondition::ALL, multiple: true)]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post",
              "land_proposal:patch",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    #[ApiProperty(openapiContext: [
        'type' => 'array',
        'enum' => LandSharingCondition::ALL,
        'example' => LandSharingCondition::ALL
    ])]
    private ?array $sharingConditions = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["land_proposal:collection",
              "land_proposal:get",
              "land_proposal:post:output",
              "land_proposal:patch:output",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    private ?DateTimeImmutable $publishedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["land_proposal:collection",
              "land_proposal:get",
              "land_proposal:post:output",
              "land_proposal:patch:output",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    private ?DateTimeImmutable $archivedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post:output",
              "land_proposal:patch:output",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    private ?DateTimeImmutable $expirationDate = null;

    #[Groups(["land_proposal:collection",
              "land_proposal:collection-public",
              "land_proposal:get",
              "land_proposal:post:output",
              "land_proposal:patch:output",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
    }

    #[Groups(["land_proposal:collection",
              "land_proposal:get",
              "land_proposal:post:output",
              "land_proposal:patch:output",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(["land_proposal:collection",
              "land_proposal:get",
              "land_proposal:post:output",
              "land_proposal:patch:output",
              "land_proposal:publish:output",
              "land_proposal:archive:output"])]
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }


    public function getDescription(): ?array
    {
        return $this->description;
    }

    public function setDescription(?array $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSoilType(): ?string
    {
        return $this->soilType;
    }

    public function setSoilType(string $soilType): static
    {
        $this->soilType = $soilType;

        return $this;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    public function setOrientation(string $orientation): static
    {
        $this->orientation = $orientation;

        return $this;
    }

    public function getHasParking(): ?bool
    {
        return $this->hasParking;
    }

    public function setHasParking(bool $hasParking): static
    {
        $this->hasParking = $hasParking;

        return $this;
    }

    public function getHasTools(): ?bool
    {
        return $this->hasTools;
    }

    public function setHasTools(bool $hasTools): static
    {
        $this->hasTools = $hasTools;

        return $this;
    }

    public function getHasShed(): ?bool
    {
        return $this->hasShed;
    }

    public function setHasShed(bool $hasShed): static
    {
        $this->hasShed = $hasShed;

        return $this;
    }

    public function getHasWaterPoint(): ?bool
    {
        return $this->hasWaterPoint;
    }

    public function setHasWaterPoint(bool $hasWaterPoint): static
    {
        $this->hasWaterPoint = $hasWaterPoint;

        return $this;
    }

    public function getHasIndependentAccess(): ?bool
    {
        return $this->hasIndependentAccess;
    }

    public function setHasIndependentAccess(bool $hasIndependentAccess): static
    {
        $this->hasIndependentAccess = $hasIndependentAccess;

        return $this;
    }

    public function getGardenState(): ?string
    {
        return $this->gardenState;
    }

    public function setGardenState(string $gardenState): static
    {
        $this->gardenState = $gardenState;

        return $this;
    }

    public function getPreferredInteractionMode(): ?string
    {
        return $this->preferredInteractionMode;
    }

    public function setPreferredInteractionMode(string $preferredInteractionMode): static
    {
        $this->preferredInteractionMode = $preferredInteractionMode;

        return $this;
    }

    public function getGardeningLevel(): ?string
    {
        return $this->gardeningLevel;
    }

    public function setGardeningLevel(string $gardeningLevel): static
    {
        $this->gardeningLevel = $gardeningLevel;

        return $this;
    }

    public function getLookingForGardenerLevel(): ?string
    {
        return $this->lookingForGardenerLevel;
    }

    public function setLookingForGardenerLevel(string $lookingForGardenerLevel): static
    {
        $this->lookingForGardenerLevel = $lookingForGardenerLevel;

        return $this;
    }

    public function getGardenTotalSurface(): ?int
    {
        return $this->gardenTotalSurface;
    }

    public function setGardenTotalSurface(int $gardenTotalSurface): static
    {
        $this->gardenTotalSurface = $gardenTotalSurface;

        return $this;
    }

    public function getFoodSecurityParticipation(): ?bool
    {
        return $this->foodSecurityParticipation;
    }

    public function setFoodSecurityParticipation(bool $foodSecurityParticipation): static
    {
        $this->foodSecurityParticipation = $foodSecurityParticipation;

        return $this;
    }

    public function getLand(): ?Land
    {
        return $this->land;
    }

    public function setLand(?Land $land): static
    {
        $this->land = $land;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getSharingConditions(): ?array
    {
        return $this->sharingConditions;
    }

    public function setSharingConditions(?array $sharingConditions): static
    {
        $this->sharingConditions = $sharingConditions;

        return $this;
    }

    public function getPublishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?DateTimeImmutable $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getArchivedAt(): ?DateTimeImmutable
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?DateTimeImmutable $archivedAt): static
    {
        $this->archivedAt = $archivedAt;

        return $this;
    }

    public function getExpirationDate(): ?DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?DateTimeImmutable $expirationDate): static
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }
}

