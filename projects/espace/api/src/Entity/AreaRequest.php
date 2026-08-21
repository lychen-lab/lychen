<?php

namespace App\Entity;

use App\Repository\AreaRequestRepository;
use App\Workflow\AreaRequest\AreaRequestWorkflow;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUuidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Positive;

#[ORM\Entity(repositoryClass: AreaRequestRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AreaRequest extends AbstractIdOrmAndUuidApiIdentified
{
    use CreatedAtTrait;

    #[ORM\Column(length: 255)]
    private ?string $state = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Choice(choices: AreaRequestWorkflow::PLACES)]
    private string $place = AreaRequestWorkflow::PLACE_DRAFT;

    #[ORM\ManyToOne(inversedBy: 'areaRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $requester = null;

    #[ORM\Column(nullable: true)]
    #[Positive]
    private ?int $minimalSurfaceRequested = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $archivedAt = null;

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

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;

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

    public function getRequester(): ?Person
    {
        return $this->requester;
    }

    public function setRequester(?Person $requester): static
    {
        $this->requester = $requester;

        return $this;
    }

    public function getMinimalSurfaceRequested(): ?int
    {
        return $this->minimalSurfaceRequested;
    }

    public function setMinimalSurfaceRequested(?int $minimalSurfaceRequested): static
    {
        $this->minimalSurfaceRequested = $minimalSurfaceRequested;

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

    public function getArchivedAt(): ?\DateTimeImmutable
    {
        return $this->archivedAt;
    }

    public function setArchivedAt(?\DateTimeImmutable $archivedAt): static
    {
        $this->archivedAt = $archivedAt;

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
}
