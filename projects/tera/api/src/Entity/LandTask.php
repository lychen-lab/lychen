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
use App\Filter\LandFilter;
use App\Processor\WorkflowTransitionProcessor;
use App\Repository\LandTaskRepository;
use App\Security\Interface\LandAwareInterface;
use App\Security\Voter\LandTaskVoter;
use App\Workflow\LandTask\LandTaskWorkflowPlace;
use App\Workflow\LandTask\LandTaskWorkflowTransition;
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

#[ORM\Entity(repositoryClass: LandTaskRepository::class)]
#[ApiResource()]
#[Post(
    normalizationContext   : ['groups' => ['land_task:post', 'land_task:post:output']],
    denormalizationContext : ['groups' => ['land_task:post', 'land_task:post:input']],
    securityPostDenormalize: "is_granted('" . LandTaskVoter::POST . "', object)",
)]
#[Patch(
    normalizationContext  : ['groups' => ['land_task:patch', 'land_task:patch:output']],
    denormalizationContext: ['groups' => ['land_task:patch', 'land_task:patch:input']],
    security              : "is_granted('" . LandTaskVoter::PATCH . "', object)"
)]
#[Delete(
    security: "is_granted('" . LandTaskVoter::DELETE . "', object)"
)]
#[Get(
    normalizationContext: ['groups' => ['land_task:get']],
    security            : "is_granted('" . LandTaskVoter::GET . "', object)",
)]
#[GetCollection(
    normalizationContext: ['groups' => ['land_task:collection']],
    security            : "is_granted('" . LandTaskVoter::COLLECTION . "')",
    parameters          : [
        new QueryParameter(
            key   : 'land',
            filter: LandFilter::class,
        ),
        'order[:property]' => new QueryParameter(filter: 'land_task.order_filter'),
        new QueryParameter(key    : 'state',
                           schema : ['type' => 'string',
                                     'enum' => LandTaskWorkflowPlace::PLACES,
                                     'example' => LandTaskWorkflowPlace::TO_BE_DONE],
                           openApi: new Parameter(name           : 'state',
                                                  in             : 'query',
                                                  description    : 'Filter by state',
                                                  required       : false,
                                                  allowEmptyValue: true),
                           filter : 'land_task.state_filter'),
    ]
)]
#[Patch(
    uriTemplate           : '/land_tasks/{ulid}/' . LandTaskWorkflowTransition::MARK_AS_DONE,
    options               : ['transition' => LandTaskWorkflowTransition::MARK_AS_DONE],
    normalizationContext  : ['groups' => ['land_task:mark-as-done', 'land_task:mark-as-done:output']],
    denormalizationContext: ['groups' => ['land_task:mark-as-done', 'land_task:mark-as-done:input']],
    security              : "is_granted('" . LandTaskVoter::MARK_AS_DONE . "', object)",
    processor             : WorkflowTransitionProcessor::class
)]
#[Patch(
    uriTemplate           : '/land_tasks/{ulid}/' . LandTaskWorkflowTransition::MARK_AS_IN_PROGRESS,
    options               : ['transition' => LandTaskWorkflowTransition::MARK_AS_IN_PROGRESS],
    normalizationContext  : ['groups' => ['land_task:mark-as-in-progress', 'land_task:mark-as-in-progress:output']],
    denormalizationContext: ['groups' => ['land_task:mark-as-in-progress', 'land_task:mark-as-in-progress:input']],
    security              : "is_granted('" . LandTaskVoter::MARK_AS_IN_PROGRESS . "', object)",
    processor             : WorkflowTransitionProcessor::class
)]
#[ORM\HasLifecycleCallbacks]
class LandTask extends AbstractIdOrmAndUlidApiIdentified implements LandAwareInterface
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Column(length: 255)]
    #[Groups(["land_task:collection", "land_task:get", "land_task:patch", "land_task:post"])]
    #[Assert\NotBlank()]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'landTasks')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["land_task:get", "land_task:post", "land_task:patch:output"])]
    private ?Land $land = null;

    /**
     * @var array|null Tiptap JSON Object
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(["land_task:get", "land_task:patch", "land_task:post"])]
    private ?array $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\GreaterThanOrEqual(propertyPath: "startDate")]
    #[Groups(["land_task:collection", "land_task:get", "land_task:patch", "land_task:post"])]
    private ?DateTimeInterface $dueDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(["land_task:collection", "land_task:get", "land_task:patch", "land_task:post"])]
    private ?DateTimeInterface $startDate = null;

    #[ORM\ManyToOne(inversedBy: 'landTasks')]
    #[Groups(["land_task:collection", "land_task:get", "land_task:patch", "land_task:post"])]
    private ?LandArea $landArea = null;

    #[ORM\Column(length: 255)]
    #[Groups(["land_task:collection", "land_task:get", "land_task:patch:output", "land_task:post:output"])]
    #[Assert\Choice(choices: LandTaskWorkflowPlace::PLACES)]
    #[ApiProperty(openapiContext: ['type' => 'string',
                                   'enum' => LandTaskWorkflowPlace::PLACES,
                                   'example' => LandTaskWorkflowPlace::TO_BE_DONE])]
    private ?string $state = LandTaskWorkflowPlace::TO_BE_DONE;

    #[Groups(["land_task:collection", "land_task:get", "land_task:patch:output", "land_task:post:output"])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
    }

    #[Groups(["land_task:collection", "land_task:get", "land_task:patch:output", "land_task:post:output"])]
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(["land_task:collection", "land_task:get", "land_task:patch:output", "land_task:post:output"])]
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

    public function getLand(): ?Land
    {
        return $this->land;
    }

    public function setLand(?Land $land): static
    {
        $this->land = $land;

        return $this;
    }

    public function getContent(): ?array
    {
        return $this->content;
    }

    public function setContent(?array $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getDueDate(): ?DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(?DateTimeInterface $dueDate): static
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getStartDate(): ?DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getLandArea(): ?LandArea
    {
        return $this->landArea;
    }

    public function setLandArea(?LandArea $landArea): static
    {
        $this->landArea = $landArea;

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
}
