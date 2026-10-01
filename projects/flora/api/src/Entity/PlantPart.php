<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\PlantPartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: PlantPartRepository::class)]
#[ApiResource]
#[Post(
    normalizationContext  : ['groups' => ['plant_part:post', 'plant_part:post:output']],
    denormalizationContext: ['groups' => ['plant_part:post', 'plant_part:post:input']]
)]
#[Patch(
    normalizationContext  : ['groups' => ['plant_part:patch', 'plant_part:patch:output']],
    denormalizationContext: ['groups' => ['plant_part:patch', 'plant_part:patch:input']]
)]
#[Delete()]
#[Get(normalizationContext: ['groups' => ['plant_part:get']])]
#[GetCollection(
    normalizationContext: ['groups' => ['plant_part:collection']],
)]
#[ORM\HasLifecycleCallbacks]
class PlantPart extends AbstractIdOrmAndUlidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\ManyToOne(inversedBy: 'plantParts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Plant $plant = null;

    #[ORM\ManyToOne(inversedBy: 'plantParts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Part $part = null;

    #[ORM\Column]
    private ?bool $consumable = false;

    /**
     * @var Collection<int, ConsumptionMethod>
     */
    #[ORM\ManyToMany(targetEntity: ConsumptionMethod::class, inversedBy: 'plantParts')]
    private Collection $consumptionMethods;

    public function __construct()
    {
        parent::__construct();
        $this->consumptionMethods = new ArrayCollection();
    }

    #[Groups(['plant_part:get'])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
    }

    #[Groups(['plant_part:get'])]
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(['plant_part:get'])]
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getPlant(): ?Plant
    {
        return $this->plant;
    }

    public function setPlant(?Plant $plant): static
    {
        $this->plant = $plant;

        return $this;
    }

    public function getPart(): ?Part
    {
        return $this->part;
    }

    public function setPart(?Part $part): static
    {
        $this->part = $part;

        return $this;
    }

    public function isConsumable(): ?bool
    {
        return $this->consumable;
    }

    public function setConsumable(bool $consumable): static
    {
        $this->consumable = $consumable;

        return $this;
    }

    /**
     * @return Collection<int, ConsumptionMethod>
     */
    public function getConsumptionMethods(): Collection
    {
        return $this->consumptionMethods;
    }

    public function addConsumptionMethod(ConsumptionMethod $consumptionMethod): static
    {
        if (!$this->consumptionMethods->contains($consumptionMethod)) {
            $this->consumptionMethods->add($consumptionMethod);
        }

        return $this;
    }

    public function removeConsumptionMethod(ConsumptionMethod $consumptionMethod): static
    {
        $this->consumptionMethods->removeElement($consumptionMethod);

        return $this;
    }
}
