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
use App\Entity\Interface\StatePersonInterface;
use App\Processor\WorkflowTransitionProcessor;
use App\Repository\LandRequestRepository;
use App\Security\Voter\LandRequestVoter;
use App\Validator\UniqueStatePerPerson;
use App\Workflow\LandRequest\LandRequestWorkflowPlace;
use App\Workflow\LandRequest\LandRequestWorkflowTransition;
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

#[ORM\Entity(repositoryClass: LandRequestRepository::class)]
#[ApiResource()]
#[GetCollection(
    normalizationContext: ['groups' => ['land_request:collection']],
    security            : "is_granted('" . LandRequestVoter::COLLECTION . "')",
    parameters          : [
        'order[:property]' => new QueryParameter(
            filter: 'land_request.order_filter'
        ),
        new QueryParameter(
            key    : 'state',
            schema : [
                'type' => 'string',
                'enum' => LandRequestWorkflowPlace::PLACES,
                'example' => LandRequestWorkflowPlace::ARCHIVED,
            ],
            openApi: new Parameter(
                name           : 'state',
                in             : 'query',
                description    : 'Filter by state',
                required       : false,
                allowEmptyValue: true
            ),
            filter : 'common.collection_state_filter'),
    ]
)]
#[GetCollection(
    uriTemplate         : '/land_requests/public',
    normalizationContext: ['groups' => ['land_request:collection-public']],
    security            : "is_granted('" . LandRequestVoter::COLLECTION_PUBLIC . "')",
    parameters          : [
        'order[:property]' => new QueryParameter(
            filter: 'land_request.order_filter'
        ),
    ]
)]
#[Get(
    normalizationContext: ['groups' => ['land_request:get']],
    security            : "is_granted('" . LandRequestVoter::GET . "', object)"
)]
#[Post(
    normalizationContext  : ['groups' => ['land_request:post', 'land_request:post:output']],
    denormalizationContext: ['groups' => ['land_request:post', 'land_request:post:input']],
    security              : "is_granted('" . LandRequestVoter::POST . "')"
)]
#[Patch(
    normalizationContext  : ['groups' => ['land_request:patch', 'land_request:patch:output']],
    denormalizationContext: ['groups' => ['land_request:patch', 'land_request:patch:input']],
    security              : "is_granted('" . LandRequestVoter::PATCH . "', previous_object)"
)]
#[Patch(
    uriTemplate           : '/land_requests/{ulid}/' . LandRequestWorkflowTransition::PUBLISH,
    options               : ['transition' => LandRequestWorkflowTransition::PUBLISH],
    normalizationContext  : ['groups' => ['land_request:publish', 'land_request:publish:output']],
    denormalizationContext: ['groups' => ['land_request:publish', 'land_request:publish:input']],
    security              : "is_granted('" . LandRequestVoter::PUBLISH . "', previous_object)",
    processor             : WorkflowTransitionProcessor::class)]
#[Patch(
    uriTemplate           : '/land_requests/{ulid}/' . LandRequestWorkflowTransition::ARCHIVE,
    options               : ['transition' => LandRequestWorkflowTransition::ARCHIVE],
    normalizationContext  : ['groups' => ['land_request:archive', 'land_request:archive:output']],
    denormalizationContext: ['groups' => ['land_request:archive', 'land_request:archive:input']],
    security              : "is_granted('" . LandRequestVoter::ARCHIVE . "', previous_object)",
    processor             : WorkflowTransitionProcessor::class)]
#[Delete(security: "is_granted('" . LandRequestVoter::DELETE . "', object)")]
#[ORM\HasLifecycleCallbacks]
#[UniqueStatePerPerson(states: [LandRequestWorkflowPlace::DRAFT, LandRequestWorkflowPlace::PUBLISHED])]
class LandRequest extends AbstractIdOrmAndUlidApiIdentified implements StatePersonInterface
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\ManyToOne(inversedBy: 'landRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $person = null;

    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: LandRequestWorkflowPlace::PLACES)]
    #[Groups(["land_request:collection",
              "land_request:collection-public",
              "land_request:get",
              "land_request:post:output",
              "land_request:patch:output",
              "land_request:publish:output",
              "land_request:archive:output"])]
    private ?string $state = LandRequestWorkflowPlace::DRAFT;

    /**
     * @var array|null Tiptap JSON Object
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(["land_request:collection",
              "land_request:collection-public",
              "land_request:get",
              "land_request:post",
              "land_request:patch",
              "land_request:publish:output",
              "land_request:archive:output"])]
    private ?array $message = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["land_request:collection",
              "land_request:collection-public",
              "land_request:get",
              "land_request:post",
              "land_request:patch",
              "land_request:publish:output",
              "land_request:archive:output"])]
    private ?int $minimumSurfaceWanted = null;

    #[ORM\Column(length: 30)]
    #[Assert\Choice(choices: GardeningLevel::ALL)]
    #[Groups(["land_request:collection",
              "land_request:collection-public",
              "land_request:get",
              "land_request:post",
              "land_request:patch",
              "land_request:publish:output",
              "land_request:archive:output"])]
    private ?string $gardeningLevel = GardeningLevel::BEGINNER;

    #[ORM\Column]
    #[Groups(["land_request:collection",
              "land_request:collection-public",
              "land_request:get",
              "land_request:post",
              "land_request:patch",
              "land_request:publish:output",
              "land_request:archive:output"])]
    private ?bool $hasTools = false;

    #[ORM\Column(length: 255)]
    #[Groups(["land_request:collection",
              "land_request:collection-public",
              "land_request:get",
              "land_request:post",
              "land_request:patch",
              "land_request:publish:output",
              "land_request:archive:output"])]
    private ?string $title = null;

    #[ORM\Column(length: 30)]
    #[Assert\Choice(choices: LandInteractionMode::ALL)]
    #[Groups(["land_request:collection",
              "land_request:collection-public",
              "land_request:get",
              "land_request:post",
              "land_request:patch",
              "land_request:publish:output",
              "land_request:archive:output"])]
    #[ApiProperty(openapiContext: [
        'type' => 'array',
        'enum' => LandInteractionMode::ALL,
        'example' => LandInteractionMode::ALL
    ])]
    private ?string $preferredInteractionMode = LandInteractionMode::NO_PREFERENCE;

    #[ORM\Column]
    #[Groups(["land_request:collection",
              "land_request:collection-public",
              "land_request:get",
              "land_request:post",
              "land_request:patch",
              "land_request:publish:output",
              "land_request:archive:output"])]
    private ?bool $supportsLocalFoodSecurity = false;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Assert\Choice(choices: LandSharingCondition::ALL, multiple: true)]
    #[Groups(["land_request:collection",
              "land_request:collection-public",
              "land_request:get",
              "land_request:post",
              "land_request:patch",
              "land_request:publish:output",
              "land_request:archive:output"])]
    #[ApiProperty(openapiContext: [
        'type' => 'array',
        'enum' => LandSharingCondition::ALL,
        'example' => LandSharingCondition::ALL
    ])]
    private ?array $sharingConditions = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["land_request:collection",
              "land_request:collection-public",
              "land_request:get",
              "land_request:post:output",
              "land_request:patch:output",
              "land_request:publish:output",
              "land_request:archive:output"])]
    private ?DateTimeImmutable $publishedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["land_request:collection",
              "land_request:collection-public",
              "land_request:get",
              "land_request:post:output",
              "land_request:patch:output",
              "land_request:publish:output",
              "land_request:archive:output"])]
    private ?DateTimeImmutable $archivedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["land_request:collection",
              "land_request:collection-public",
              "land_request:get",
              "land_request:post:output",
              "land_request:patch:output",
              "land_request:publish:output",
              "land_request:archive:output"])]
    private ?DateTimeImmutable $expirationDate = null;

    #[Groups(["land_request:collection",
              "land_request:collection-public",
              "land_request:get",
              "land_request:post:output",
              "land_request:patch:output",
              "land_request:publish:output",
              "land_request:archive:output"])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
    }

    #[Groups(["land_request:collection",
              "land_request:collection-public",
              "land_request:get",
              "land_request:post:output",
              "land_request:patch:output",
              "land_request:publish:output",
              "land_request:archive:output"])]
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(["land_request:collection",
              "land_request:collection-public",
              "land_request:get",
              "land_request:post:output",
              "land_request:patch:output",
              "land_request:publish:output",
              "land_request:archive:output"])]
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): static
    {
        $this->person = $person;

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

    public function getMessage(): ?array
    {
        return $this->message;
    }

    public function setMessage(?array $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getMinimumSurfaceWanted(): ?int
    {
        return $this->minimumSurfaceWanted;
    }

    public function setMinimumSurfaceWanted(?int $minimumSurfaceWanted): static
    {
        $this->minimumSurfaceWanted = $minimumSurfaceWanted;

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

    public function getHasTools(): ?bool
    {
        return $this->hasTools;
    }

    public function setHasTools(bool $hasTools): static
    {
        $this->hasTools = $hasTools;

        return $this;
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

    public function getPreferredInteractionMode(): ?string
    {
        return $this->preferredInteractionMode;
    }

    public function setPreferredInteractionMode(?string $preferredInteractionMode): static
    {
        $this->preferredInteractionMode = $preferredInteractionMode;

        return $this;
    }

    public function getSupportsLocalFoodSecurity(): ?bool
    {
        return $this->supportsLocalFoodSecurity;
    }

    public function setSupportsLocalFoodSecurity(bool $supportsLocalFoodSecurity): static
    {
        $this->supportsLocalFoodSecurity = $supportsLocalFoodSecurity;

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
