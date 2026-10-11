<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\PartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PartRepository::class)]
#[ApiResource]
#[Post(
    normalizationContext   : ['groups' => ['part:post', 'part:post:output']],
    denormalizationContext : ['groups' => ['part:post', 'part:post:input']]
)]
#[Patch(
    normalizationContext  : ['groups' => ['part:patch', 'part:patch:output']],
    denormalizationContext: ['groups' => ['part:patch', 'part:patch:input']]
)]
#[Delete()]
#[Get(normalizationContext: ['groups' => ['part:get']])]
#[GetCollection(
    normalizationContext: ['groups' => ['part:collection']],
)]
#[ORM\HasLifecycleCallbacks]
class Part extends AbstractIdOrmAndUlidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[Groups(['part:get'])]
    #[ORM\Column(length: 100, unique: true)]
    private ?string $code = null;

    /**
     * @var Collection<int, PlantPart>
     */
    #[ORM\OneToMany(targetEntity: PlantPart::class, mappedBy: 'part', orphanRemoval: true)]
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

    #[Groups(['part:get'])]
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(['part:get'])]
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
            $plantPart->setPart($this);
        }

        return $this;
    }

    public function removePlantPart(PlantPart $plantPart): static
    {
        if ($this->plantParts->removeElement($plantPart)) {
            // set the owning side to null (unless already changed)
            if ($plantPart->getPart() === $this) {
                $plantPart->setPart(null);
            }
        }

        return $this;
    }
}
