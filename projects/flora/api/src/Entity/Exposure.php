<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\ExposureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ExposureRepository::class)]
#[ApiResource]
#[Post(
    normalizationContext  : ['groups' => ['exposure:post', 'exposure:post:output']],
    denormalizationContext: ['groups' => ['exposure:post', 'exposure:post:input']]
)]
#[Patch(
    normalizationContext  : ['groups' => ['exposure:patch', 'exposure:patch:output']],
    denormalizationContext: ['groups' => ['exposure:patch', 'exposure:patch:input']]
)]
#[Delete()]
#[Get(normalizationContext: ['groups' => ['exposure:get']])]
#[GetCollection(
    normalizationContext: ['groups' => ['exposure:collection']],
)]
#[ORM\HasLifecycleCallbacks]
class Exposure extends AbstractIdOrmAndUlidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[Groups(['exposure:get'])]
    #[ORM\Column(length: 100, unique: true)]
    private ?string $code = null;

    /**
     * @var Collection<int, Cultivation>
     */
    #[ORM\OneToMany(targetEntity: Cultivation::class, mappedBy: 'exposure')]
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

    #[Groups(['exposure:get'])]
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(['exposure:get'])]
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
            $cultivation->setExposure($this);
        }

        return $this;
    }

    public function removeCultivation(Cultivation $cultivation): static
    {
        if ($this->cultivations->removeElement($cultivation)) {
            // set the owning side to null (unless already changed)
            if ($cultivation->getExposure() === $this) {
                $cultivation->setExposure(null);
            }
        }

        return $this;
    }
}
