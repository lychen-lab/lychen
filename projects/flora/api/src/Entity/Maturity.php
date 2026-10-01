<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\MaturityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MaturityRepository::class)]
#[ApiResource]
#[Post(
    normalizationContext   : ['groups' => ['maturity:post', 'maturity:post:output']],
    denormalizationContext : ['groups' => ['maturity:post', 'maturity:post:input']]
)]
#[Patch(
    normalizationContext  : ['groups' => ['maturity:patch', 'maturity:patch:output']],
    denormalizationContext: ['groups' => ['maturity:patch', 'maturity:patch:input']]
)]
#[Delete()]
#[Get(normalizationContext: ['groups' => ['maturity:get']])]
#[GetCollection(
    normalizationContext: ['groups' => ['maturity:collection']],
)]
#[ORM\HasLifecycleCallbacks]
class Maturity extends AbstractIdOrmAndUlidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[Groups(['maturity:get'])]
    #[ORM\Column(length: 100, unique: true)]
    private ?string $code = null;

    /**
     * @var Collection<int, Cultivation>
     */
    #[ORM\OneToMany(targetEntity: Cultivation::class, mappedBy: 'maturity')]
    private Collection $cultivations;

    public function __construct()
    {
        parent::__construct();
        $this->cultivations = new ArrayCollection();
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

    #[Groups(['maturity:get'])]
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(['maturity:get'])]
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @return Collection<int, Cultivation>
     */
    public function getCultivations(): Collection
    {
        return $this->cultivations;
    }

    public function addCultivation(Cultivation $cultivation): static
    {
        if (!$this->cultivations->contains($cultivation)) {
            $this->cultivations->add($cultivation);
            $cultivation->setMaturity($this);
        }

        return $this;
    }

    public function removeCultivation(Cultivation $cultivation): static
    {
        if ($this->cultivations->removeElement($cultivation)) {
            // set the owning side to null (unless already changed)
            if ($cultivation->getMaturity() === $this) {
                $cultivation->setMaturity(null);
            }
        }

        return $this;
    }
}
