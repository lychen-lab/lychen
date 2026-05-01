<?php

namespace App\Entity;

use App\Enum\AreaProposalStatus;
use App\Repository\AreaProposalRepository;
use App\Workflow\AreaProposal\AreaProposalWorkflow;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUuidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Positive;

#[ORM\Entity(repositoryClass: AreaProposalRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AreaProposal extends AbstractIdOrmAndUuidApiIdentified
{
    use CreatedAtTrait;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Choice(AreaProposalWorkflow::PLACES)]
    private ?string $place = AreaProposalWorkflow::PLACE_DRAFT;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $archivedAt = null;

    #[ORM\Column(nullable: true)]
    #[Positive]
    private ?int $surfaceTotal = null;

    #[ORM\Column(nullable: true)]
    #[Positive]
    private ?int $surfaceToShare = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(nullable: true)]
    private ?int $altitude = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $soilType = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $availableAt = null;

    #[ORM\Column(enumType: AreaProposalStatus::class)]
    private AreaProposalStatus $status = AreaProposalStatus::PendingValidation;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $workflowId = null;

    /**
     * @var Collection<int, AreaActivity>
     */
    #[ORM\ManyToMany(targetEntity: AreaActivity::class)]
    private Collection $activities;

    public function __construct()
    {
        parent::__construct();
        $this->activities = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): static
    {
        $this->place = $place;

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

    public function getSurfaceTotal(): ?int
    {
        return $this->surfaceTotal;
    }

    public function setSurfaceTotal(?int $surfaceTotal): static
    {
        $this->surfaceTotal = $surfaceTotal;

        return $this;
    }

    public function getSurfaceToShare(): ?int
    {
        return $this->surfaceToShare;
    }

    public function setSurfaceToShare(?int $surfaceToShare): static
    {
        $this->surfaceToShare = $surfaceToShare;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getAltitude(): ?int
    {
        return $this->altitude;
    }

    public function setAltitude(?int $altitude): static
    {
        $this->altitude = $altitude;

        return $this;
    }

    /**
     * @return Collection<int, AreaActivity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(AreaActivity $activity): static
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
        }

        return $this;
    }

    public function removeActivity(AreaActivity $activity): static
    {
        $this->activities->removeElement($activity);

        return $this;
    }

    public function getSoilType(): ?string
    {
        return $this->soilType;
    }

    public function setSoilType(?string $soilType): static
    {
        $this->soilType = $soilType;

        return $this;
    }

    public function getAvailableAt(): ?DateTimeImmutable
    {
        return $this->availableAt;
    }

    public function setAvailableAt(?DateTimeImmutable $availableAt): static
    {
        $this->availableAt = $availableAt;

        return $this;
    }

    public function getStatus(): AreaProposalStatus
    {
        return $this->status;
    }

    public function setStatus(AreaProposalStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getWorkflowId(): ?string
    {
        return $this->workflowId;
    }

    public function setWorkflowId(?string $workflowId): static
    {
        $this->workflowId = $workflowId;

        return $this;
    }
}
