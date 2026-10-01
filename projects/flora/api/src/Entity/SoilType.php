<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\SoilTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SoilTypeRepository::class)]
#[ApiResource]
#[Post(
    normalizationContext  : ['groups' => ['soil_type:post', 'soil_type:post:output']],
    denormalizationContext: ['groups' => ['soil_type:post', 'soil_type:post:input']]
)]
#[Patch(
    normalizationContext  : ['groups' => ['soil_type:patch', 'soil_type:patch:output']],
    denormalizationContext: ['groups' => ['soil_type:patch', 'soil_type:patch:input']]
)]
#[Delete()]
#[Get(normalizationContext: ['groups' => ['soil_type:get']])]
#[GetCollection(
    normalizationContext: ['groups' => ['soil_type:collection']],
)]
#[ORM\HasLifecycleCallbacks]
class SoilType extends AbstractIdOrmAndUlidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[Groups(['soil_type:get'])]
    #[ORM\Column(length: 100, unique: true)]
    private ?string $code = null;

    /**
     * @var Collection<int, Plant>
     */
    #[ORM\ManyToMany(targetEntity: Plant::class, mappedBy: 'soilTypes')]
    private Collection $plants;

    public function __construct()
    {
        parent::__construct();
        $this->plants = new ArrayCollection();
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

    #[Groups(['soil_type:get'])]
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(['soil_type:get'])]
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, Plant>
     */
    public function getPlants(): Collection
    {
        return $this->plants;
    }

    public function addPlant(Plant $plant): static
    {
        if (!$this->plants->contains($plant)) {
            $this->plants->add($plant);
            $plant->addSoilType($this);
        }

        return $this;
    }

    public function removePlant(Plant $plant): static
    {
        if ($this->plants->removeElement($plant)) {
            $plant->removeSoilType($this);
        }

        return $this;
    }
}
