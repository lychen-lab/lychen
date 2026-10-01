<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\ConsumptionMethodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ConsumptionMethodRepository::class)]
#[ApiResource]
#[Post(
    normalizationContext  : ['groups' => ['consumption_method:post', 'consumption_method:post:output']],
    denormalizationContext: ['groups' => ['consumption_method:post', 'consumption_method:post:input']]
)]
#[Patch(
    normalizationContext  : ['groups' => ['consumption_method:patch', 'consumption_method:patch:output']],
    denormalizationContext: ['groups' => ['consumption_method:patch', 'consumption_method:patch:input']]
)]
#[Delete()]
#[Get(normalizationContext: ['groups' => ['consumption_method:get']])]
#[GetCollection(
    normalizationContext: ['groups' => ['consumption_method:collection']],
)]
#[ORM\HasLifecycleCallbacks]
class ConsumptionMethod extends AbstractIdOrmAndUlidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[Groups(['consumption_method:get'])]
    #[ORM\Column(length: 100, unique: true)]
    private ?string $code = null;

    /**
     * @var Collection<int, PlantPart>
     */
    #[ORM\ManyToMany(targetEntity: PlantPart::class, mappedBy: 'consumptionMethods')]
    private Collection $plantParts;

    public function __construct()
    {
        parent::__construct();
        $this->plantParts = new ArrayCollection();
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    #[Groups(['consumption_method:get'])]
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(['consumption_method:get'])]
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, PlantPart>
     */
    public function getPlantParts(): Collection
    {
        return $this->plantParts;
    }

    public function addPlantPart(PlantPart $plantPart): static
    {
        if (!$this->plantParts->contains($plantPart)) {
            $this->plantParts->add($plantPart);
            $plantPart->addConsumptionMethod($this);
        }

        return $this;
    }

    public function removePlantPart(PlantPart $plantPart): static
    {
        if ($this->plantParts->removeElement($plantPart)) {
            $plantPart->removeConsumptionMethod($this);
        }

        return $this;
    }
}
