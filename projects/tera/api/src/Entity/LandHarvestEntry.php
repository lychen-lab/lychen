<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Constant\HarvestQuality;
use App\Filter\LandFilter;
use App\Repository\LandHarvestEntryRepository;
use App\Security\Interface\LandAwareInterface;
use App\Security\Voter\LandHarvestEntryVoter;
use App\Validator\PlantUlid;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LandHarvestEntryRepository::class)]
#[ApiResource()]
#[Post(
    normalizationContext   : ['groups' => ['land_harvest_entry:post', 'land_harvest_entry:post:output']],
    denormalizationContext : ['groups' => ['land_harvest_entry:post', 'land_harvest_entry:post:input']],
    securityPostDenormalize: "is_granted('".LandHarvestEntryVoter::POST."', object)",
)]
#[Patch(
    normalizationContext  : ['groups' => ['land_harvest_entry:patch', 'land_harvest_entry:patch:output']],
    denormalizationContext: ['groups' => ['land_harvest_entry:patch', 'land_harvest_entry:patch:input']],
    security              : "is_granted('".LandHarvestEntryVoter::PATCH."', object)"
)]
#[Delete(
    security: "is_granted('".LandHarvestEntryVoter::DELETE."', object)"
)]
#[Get(
    normalizationContext: ['groups' => ['land_harvest_entry:get']],
    security            : "is_granted('".LandHarvestEntryVoter::GET."', object)",
)]
#[GetCollection(
    normalizationContext: ['groups' => ['land_harvest_entry:collection']],
    security            : "is_granted('".LandHarvestEntryVoter::COLLECTION."')",
    parameters          : [
        new QueryParameter(
            key   : 'land',
            filter: LandFilter::class,
        ),
        'order[:property]' => new QueryParameter(filter: 'land_harvest_entry.order_filter'),
    ]
)]
#[ORM\HasLifecycleCallbacks]
class LandHarvestEntry extends AbstractIdOrmAndUlidApiIdentified implements LandAwareInterface
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[Groups(['land_harvest_entry:collection',
        'land_harvest_entry:get',
        'land_harvest_entry:post'])]
    #[ORM\ManyToOne(inversedBy: 'landHarvestEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Land $land = null;

    #[Groups(['land_harvest_entry:collection',
        'land_harvest_entry:get',
        'land_harvest_entry:patch',
        'land_harvest_entry:post'])]
    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    private ?int $weight = null;

    /**
     * @var array|null Tiptap JSON Object
     */
    #[Groups(['land_harvest_entry:collection',
        'land_harvest_entry:get',
        'land_harvest_entry:patch',
        'land_harvest_entry:post'])]
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $notes = null;

    #[Groups(['land_harvest_entry:collection',
        'land_harvest_entry:get',
        'land_harvest_entry:patch',
        'land_harvest_entry:post'])]
    #[ORM\Column]
    #[Assert\LessThanOrEqual('today')]
    private ?\DateTimeImmutable $harvestedAt = null;

    #[Groups(['land_harvest_entry:collection',
        'land_harvest_entry:get',
        'land_harvest_entry:patch',
        'land_harvest_entry:post'])]
    #[ORM\Column(length: 255)]
    #[Assert\Choice(choices: HarvestQuality::ALL)]
    #[ApiProperty(openapiContext: [
        'type' => 'string',
        'enum' => HarvestQuality::ALL,
        'example' => HarvestQuality::GOOD,
    ])]
    private ?string $quality = HarvestQuality::STANDARD;

    #[Groups(['land_harvest_entry:collection',
        'land_harvest_entry:get',
        'land_harvest_entry:patch',
        'land_harvest_entry:post'])]
    #[ORM\Column(type: UlidType::NAME)]
    #[PlantUlid]
    private ?Ulid $plantId = null;

    #[Groups(['land_harvest_entry:collection',
        'land_harvest_entry:get',
        'land_harvest_entry:patch:output',
        'land_harvest_entry:post:output'])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
    }

    #[Groups(['land_harvest_entry:collection',
        'land_harvest_entry:get',
        'land_harvest_entry:patch:output',
        'land_harvest_entry:post:output'])]
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(['land_harvest_entry:collection',
        'land_harvest_entry:get',
        'land_harvest_entry:patch:output',
        'land_harvest_entry:post:output'])]
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
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

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getNotes(): ?array
    {
        return $this->notes;
    }

    public function setNotes(?array $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getHarvestedAt(): ?\DateTimeImmutable
    {
        return $this->harvestedAt;
    }

    public function setHarvestedAt(\DateTimeImmutable $harvestedAt): static
    {
        $this->harvestedAt = $harvestedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setHarvestedAtValue(): void
    {
        if (null === $this->harvestedAt) {
            $this->harvestedAt = new \DateTimeImmutable();
        }
    }

    public function getQuality(): ?string
    {
        return $this->quality;
    }

    public function setQuality(string $quality): static
    {
        $this->quality = $quality;

        return $this;
    }

    public function getPlantId(): ?Ulid
    {
        return $this->plantId;
    }

    public function setPlantId(Ulid $plantId): static
    {
        $this->plantId = $plantId;

        return $this;
    }
}
