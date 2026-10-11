<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Filter\LandFilter;
use App\Repository\LandGreenhouseRepository;
use App\Security\Interface\LandAwareInterface;
use App\Security\Voter\LandGreenhouseVoter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LandGreenhouseRepository::class)]
#[ApiResource]
#[Post(
    normalizationContext   : ['groups' => ['land_greenhouse:post', 'land_greenhouse:post:output']],
    denormalizationContext : ['groups' => ['land_greenhouse:post', 'land_greenhouse:post:input']],
    securityPostDenormalize: "is_granted('".LandGreenhouseVoter::POST."', object)")]
#[Patch(
    normalizationContext  : ['groups' => ['land_greenhouse:patch', 'land_greenhouse:patch:output']],
    denormalizationContext: ['groups' => ['land_greenhouse:patch', 'land_greenhouse:patch:input']],
    security              : "is_granted('".LandGreenhouseVoter::PATCH."', previous_object)")]
#[Delete(
    security: "is_granted('".LandGreenhouseVoter::DELETE."', object)"
)]
#[Get(
    normalizationContext: ['groups' => ['land_greenhouse:get']],
    security            : "is_granted('".LandGreenhouseVoter::GET."', object)"
)]
#[GetCollection(
    normalizationContext: ['groups' => ['land_greenhouse:collection']],
    security            : "is_granted('".LandGreenhouseVoter::COLLECTION."')",
    parameters          : [
        new QueryParameter(
            key   : 'land',
            filter: LandFilter::class,
        ),
    ]
)]
#[ORM\HasLifecycleCallbacks]
class LandGreenhouse extends AbstractIdOrmAndUlidApiIdentified implements LandAwareInterface
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Column(length: 255)]
    #[Groups(['land_greenhouse:collection',
        'land_greenhouse:get',
        'land_greenhouse:patch',
        'land_greenhouse:post'])]
    #[Assert\NotBlank()]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['land_greenhouse:collection',
        'land_greenhouse:get',
        'land_greenhouse:patch',
        'land_greenhouse:post'])]
    private ?\DateTimeInterface $constructionDate = null;

    /**
     * @var Collection<int, LandArea>
     */
    #[ORM\OneToMany(targetEntity: LandArea::class, mappedBy: 'landGreenhouse')]
    #[Groups(['land_greenhouse:collection', 'land_greenhouse:get'])]
    private Collection $landAreas;

    #[ORM\ManyToOne(inversedBy: 'landGreenhouses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['land_greenhouse:get', 'land_greenhouse:post', 'land_greenhouse:patch:output'])]
    private ?Land $land = null;

    #[ORM\OneToOne(mappedBy: 'landGreenhouse', cascade: ['persist', 'remove'])]
    #[Groups(['land_greenhouse:collection',
        'land_greenhouse:get',
        'land_greenhouse:post:output',
        'land_greenhouse:patch:output'])]
    private ?LandGreenhouseParameter $landGreenhouseParameter = null;

    #[ORM\OneToOne(mappedBy: 'landGreenhouse', cascade: ['persist', 'remove'])]
    #[Groups(['land_greenhouse:collection',
        'land_greenhouse:get',
        'land_greenhouse:post:output',
        'land_greenhouse:patch:output'])]
    private ?LandGreenhouseSetting $landGreenhouseSetting = null;

    public function __construct()
    {
        parent::__construct();
        $this->landAreas = new ArrayCollection();
        $this->setLandGreenhouseSetting(new LandGreenhouseSetting());
        $this->setLandGreenhouseParameter(new LandGreenhouseParameter());
    }

    #[Groups(['land_greenhouse:collection',
        'land_greenhouse:get',
        'land_greenhouse:patch:output',
        'land_greenhouse:post:output'])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
    }

    #[Groups(['land_greenhouse:collection',
        'land_greenhouse:get',
        'land_greenhouse:patch:output',
        'land_greenhouse:post:output'])]
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(['land_greenhouse:collection',
        'land_greenhouse:get',
        'land_greenhouse:patch:output',
        'land_greenhouse:post:output'])]
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getConstructionDate(): ?\DateTimeInterface
    {
        return $this->constructionDate;
    }

    public function setConstructionDate(?\DateTimeInterface $constructionDate): static
    {
        $this->constructionDate = $constructionDate;

        return $this;
    }

    /**
     * @return Collection<int, LandArea>
     */
    public function getLandAreas(): Collection
    {
        return $this->landAreas;
    }

    public function addLandArea(LandArea $landArea): static
    {
        if (!$this->landAreas->contains($landArea)) {
            $this->landAreas->add($landArea);
            $landArea->setLandGreenhouse($this);
        }

        return $this;
    }

    public function removeLandArea(LandArea $landArea): static
    {
        if ($this->landAreas->removeElement($landArea)) {
            // set the owning side to null (unless already changed)
            if ($landArea->getLandGreenhouse() === $this) {
                $landArea->setLandGreenhouse(null);
            }
        }

        return $this;
    }

    public function getLand(): ?Land
    {
        return $this->land;
    }

    public function setLand(?Land $land): static
    {
        $this->land = $land;

        return $this;
    }

    public function getLandGreenhouseParameter(): ?LandGreenhouseParameter
    {
        return $this->landGreenhouseParameter;
    }

    public function setLandGreenhouseParameter(LandGreenhouseParameter $landGreenhouseParameter): static
    {
        // set the owning side of the relation if necessary
        if ($landGreenhouseParameter->getLandGreenhouse() !== $this) {
            $landGreenhouseParameter->setLandGreenhouse($this);
        }

        $this->landGreenhouseParameter = $landGreenhouseParameter;

        return $this;
    }

    public function getLandGreenhouseSetting(): ?LandGreenhouseSetting
    {
        return $this->landGreenhouseSetting;
    }

    public function setLandGreenhouseSetting(LandGreenhouseSetting $landGreenhouseSetting): static
    {
        // set the owning side of the relation if necessary
        if ($landGreenhouseSetting->getLandGreenhouse() !== $this) {
            $landGreenhouseSetting->setLandGreenhouse($this);
        }

        $this->landGreenhouseSetting = $landGreenhouseSetting;

        return $this;
    }
}
