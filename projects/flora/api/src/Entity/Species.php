<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\SpeciesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SpeciesRepository::class)]
#[ApiResource]
#[Post(
    normalizationContext   : ['groups' => ['species:post', 'species:post:output']],
    denormalizationContext : ['groups' => ['species:post', 'species:post:input']]
)]
#[Patch(
    normalizationContext  : ['groups' => ['species:patch', 'species:patch:output']],
    denormalizationContext: ['groups' => ['species:patch', 'species:patch:input']]
)]
#[Delete()]
#[Get(normalizationContext: ['groups' => ['species:get']])]
#[GetCollection(
    normalizationContext: ['groups' => ['species:collection']],
)]
#[ORM\HasLifecycleCallbacks]
class Species extends AbstractIdOrmAndUlidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[Groups(['species:get'])]
    #[ORM\Column(length: 100, unique: true)]
    private ?string $code = null;

    #[ORM\ManyToOne(inversedBy: 'species')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Family $family = null;

    /**
     * @var Collection<int, Plant>
     */
    #[ORM\OneToMany(targetEntity: Plant::class, mappedBy: 'species')]
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

    #[Groups(['species:get'])]
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(['species:get'])]
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getFamily(): ?Family
    {
        return $this->family;
    }

    public function setFamily(?Family $family): static
    {
        $this->family = $family;

        return $this;
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
            $plant->setSpecies($this);
        }

        return $this;
    }

    public function removePlant(Plant $plant): static
    {
        if ($this->plants->removeElement($plant)) {
            // set the owning side to null (unless already changed)
            if ($plant->getSpecies() === $this) {
                $plant->setSpecies(null);
            }
        }

        return $this;
    }
}
