<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\LunarTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: LunarTypeRepository::class)]
#[ApiResource]
#[Post(
    normalizationContext   : ['groups' => ['lunar_type:post', 'lunar_type:post:output']],
    denormalizationContext : ['groups' => ['lunar_type:post', 'lunar_type:post:input']]
)]
#[Patch(
    normalizationContext  : ['groups' => ['lunar_type:patch', 'lunar_type:patch:output']],
    denormalizationContext: ['groups' => ['lunar_type:patch', 'lunar_type:patch:input']]
)]
#[Delete()]
#[Get(normalizationContext: ['groups' => ['lunar_type:get']])]
#[GetCollection(
    normalizationContext: ['groups' => ['lunar_type:collection']],
)]
#[ORM\HasLifecycleCallbacks]
class LunarType extends AbstractIdOrmAndUlidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[Groups(['lunar_type:get'])]
    #[ORM\Column(length: 100, unique: true)]
    private ?string $code = null;

    /**
     * @var Collection<int, Plant>
     */
    #[ORM\OneToMany(targetEntity: Plant::class, mappedBy: 'lunarType')]
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
            $plant->setLunarType($this);
        }

        return $this;
    }

    public function removePlant(Plant $plant): static
    {
        if ($this->plants->removeElement($plant)) {
            // set the owning side to null (unless already changed)
            if ($plant->getLunarType() === $this) {
                $plant->setLunarType(null);
            }
        }

        return $this;
    }

    #[Groups(['lunar_type:get'])]
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(['lunar_type:get'])]
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }
}
