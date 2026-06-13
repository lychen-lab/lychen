<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Constant\LandAreaKind;
use App\Filter\LandFilter;
use App\Repository\LandAreaRepository;
use App\Security\Interface\LandAwareInterface;
use App\Security\Voter\LandAreaVoter;
use App\Workflow\LandArea\LandAreaWorkflowPlace;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LandAreaRepository::class)]
#[ApiResource()]
#[Post(
    normalizationContext   : ['groups' => ['land_area:post', 'land_area:post:output']],
    denormalizationContext : ['groups' => ['land_area:post', 'land_area:post:input']],
    securityPostDenormalize: "is_granted('" . LandAreaVoter::POST . "', object)")]
#[Patch(
    normalizationContext  : ['groups' => ['land_area:patch', 'land_area:patch:output']],
    denormalizationContext: ['groups' => ['land_area:patch', 'land_area:patch:input']],
    security              : "is_granted('" . LandAreaVoter::PATCH . "', previous_object)")]
#[Delete(security: "is_granted('" . LandAreaVoter::DELETE . "', object)")]
#[Get(normalizationContext: ['groups' => ['land_area:get']], security: "is_granted('" . LandAreaVoter::GET . "', object)")]
#[GetCollection(
    normalizationContext: ['groups' => ['land_area:collection']],
    security            : "is_granted('" . LandAreaVoter::COLLECTION . "')",
    parameters          : [
        new QueryParameter(
            key   : 'land',
            filter: LandFilter::class,
        )
    ]
)]
#[ORM\HasLifecycleCallbacks]
class LandArea extends AbstractIdOrmAndUlidApiIdentified implements LandAwareInterface
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Column(length: 255)]
    #[Groups(["land_area:collection", "land_area:get", "land_area:patch", "land_area:post"])]
    #[Assert\NotBlank()]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'landAreas')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["land_area:get", "land_area:post"])]
    private ?Land $land = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(["land_area:collection", "land_area:get", "land_area:patch", "land_area:post"])]
    private ?string $description = null;

    #[ORM\OneToOne(mappedBy: 'landArea', cascade: ['persist', 'remove'])]
    #[Groups(["land_area:collection", "land_area:get", "land_area:patch:output", "land_area:post:output"])]
    private ?LandAreaSetting $landAreaSetting = null;

    #[ORM\OneToOne(mappedBy: 'landArea', cascade: ['persist', 'remove'])]
    #[Groups(["land_area:collection", "land_area:get", "land_area:patch:output", "land_area:post:output"])]
    private ?LandAreaParameter $landAreaParameter = null;

    #[ORM\ManyToOne(inversedBy: 'landAreas')]
    #[Groups(["land_area:collection", "land_area:get", "land_area:patch:output", "land_area:post:output"])]
    private ?LandGreenhouse $landGreenhouse = null;

    /**
     * @var Collection<int, LandTask>
     */
    #[ORM\OneToMany(targetEntity: LandTask::class, mappedBy: 'landArea')]
    #[Groups(["land_area:collection", "land_area:get", "land_area:patch:output", "land_area:post:output"])]
    private Collection $landTasks;

    #[Assert\Choice(choices: LandAreaWorkflowPlace::PLACES)]
    #[ORM\Column(length: 255)]
    #[Groups(["land_area:collection", "land_area:get", "land_area:patch:output", "land_area:post:output"])]
    #[Assert\NotBlank()]
    private ?string $state = LandAreaWorkflowPlace::ACTIVE;

    /**
     * @var Collection<int, LandCultivationPlan>
     */
    #[ORM\OneToMany(targetEntity: LandCultivationPlan::class, mappedBy: 'landArea')]
    #[Groups(["land_area:collection", "land_area:get", "land_area:patch:output", "land_area:post:output"])]
    private Collection $landCultivationPlans;

    #[ORM\Column(length: 150, options: ['default' => LandAreaKind::OPEN_SOIL])]
    #[Assert\Choice(choices: LandAreaKind::ALL)]
    #[Groups(["land_area:collection", "land_area:get", "land_area:patch", "land_area:post"])]
    private ?string $kind = LandAreaKind::OPEN_SOIL;

    public function __construct(?Ulid $ulid = null)
    {
        parent::__construct($ulid);
        $this->setLandAreaSetting(new LandAreaSetting());
        $this->setLandAreaParameter(new LandAreaParameter());
        $this->landTasks = new ArrayCollection();
        $this->landCultivationPlans = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    #[Groups(["land_area:collection", "land_area:get", "land_area:patch:output", "land_area:post:output"])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
    }

    #[Groups(["land_area:collection", "land_area:get", "land_area:patch:output", "land_area:post:output"])]
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(["land_area:collection", "land_area:get", "land_area:patch:output", "land_area:post:output"])]
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLandAreaSetting(): ?LandAreaSetting
    {
        return $this->landAreaSetting;
    }

    public function setLandAreaSetting(LandAreaSetting $landAreaSetting): static
    {
        // set the owning side of the relation if necessary
        if ($landAreaSetting->getLandArea() !== $this) {
            $landAreaSetting->setLandArea($this);
        }

        $this->landAreaSetting = $landAreaSetting;

        return $this;
    }

    public function getLandAreaParameter(): ?LandAreaParameter
    {
        return $this->landAreaParameter;
    }

    public function setLandAreaParameter(LandAreaParameter $landAreaParameter): static
    {
        // set the owning side of the relation if necessary
        if ($landAreaParameter->getLandArea() !== $this) {
            $landAreaParameter->setLandArea($this);
        }

        $this->landAreaParameter = $landAreaParameter;

        return $this;
    }

    public function getLandGreenhouse(): ?LandGreenhouse
    {
        return $this->landGreenhouse;
    }

    public function setLandGreenhouse(?LandGreenhouse $landGreenhouse): static
    {
        $this->landGreenhouse = $landGreenhouse;

        return $this;
    }

    /**
     * @return Collection<int, LandTask>
     */
    public function getLandTasks(): Collection
    {
        return $this->landTasks;
    }

    public function addLandTask(LandTask $landTask): static
    {
        if (!$this->landTasks->contains($landTask)) {
            $this->landTasks->add($landTask);
            $landTask->setLandArea($this);
        }

        return $this;
    }

    public function removeLandTask(LandTask $landTask): static
    {
        if ($this->landTasks->removeElement($landTask)) {
            // set the owning side to null (unless already changed)
            if ($landTask->getLandArea() === $this) {
                $landTask->setLandArea(null);
            }
        }

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

    /**
     * @return Collection<int, LandCultivationPlan>
     */
    public function getLandCultivationPlans(): Collection
    {
        return $this->landCultivationPlans;
    }

    public function addLandCultivationPlan(LandCultivationPlan $landCultivationPlan): static
    {
        if (!$this->landCultivationPlans->contains($landCultivationPlan)) {
            $this->landCultivationPlans->add($landCultivationPlan);
            $landCultivationPlan->setLandArea($this);
        }

        return $this;
    }

    public function removeLandCultivationPlan(LandCultivationPlan $landCultivationPlan): static
    {
        if ($this->landCultivationPlans->removeElement($landCultivationPlan)) {
            // set the owning side to null (unless already changed)
            if ($landCultivationPlan->getLandArea() === $this) {
                $landCultivationPlan->setLandArea(null);
            }
        }

        return $this;
    }

    public function getKind(): ?string
    {
        return $this->kind;
    }

    public function setKind(string $kind): static
    {
        $this->kind = $kind;

        return $this;
    }
}
