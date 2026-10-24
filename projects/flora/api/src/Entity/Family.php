<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\FamilyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FamilyRepository::class)]
#[ApiResource]
#[Post(
    normalizationContext   : ['groups' => ['family:post', 'family:post:output']],
    denormalizationContext : ['groups' => ['family:post', 'family:post:input']]
)]
#[Patch(
    normalizationContext  : ['groups' => ['family:patch', 'family:patch:output']],
    denormalizationContext: ['groups' => ['family:patch', 'family:patch:input']]
)]
#[Delete()]
#[Get(normalizationContext: ['groups' => ['family:get']])]
#[GetCollection(
    normalizationContext: ['groups' => ['family:collection']],
)]
#[ORM\HasLifecycleCallbacks]
class Family extends AbstractIdOrmAndUlidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[Groups(['family:get'])]
    #[ORM\Column(length: 100, unique: true)]
    private ?string $code = null;

    /**
     * @var Collection<int, Species>
     */
    #[ORM\OneToMany(targetEntity: Species::class, mappedBy: 'family')]
    private Collection $species;

    public function __construct()
    {
        parent::__construct();
        $this->species = new ArrayCollection();
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

    #[Groups(['family:get'])]
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(['family:get'])]
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, Species>
     */
    public function getSpecies(): Collection
    {
        return $this->species;
    }

    public function addSpecies(Species $species): static
    {
        if (!$this->species->contains($species)) {
            $this->species->add($species);
            $species->setFamily($this);
        }

        return $this;
    }

    public function removeSpecies(Species $species): static
    {
        if ($this->species->removeElement($species)) {
            // set the owning side to null (unless already changed)
            if ($species->getFamily() === $this) {
                $species->setFamily(null);
            }
        }

        return $this;
    }
}
