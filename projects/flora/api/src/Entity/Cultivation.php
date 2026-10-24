<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Constant\Month;
use App\Repository\CultivationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CultivationRepository::class)]
#[ApiResource]
#[Post(
    normalizationContext  : ['groups' => ['cultivation:post', 'cultivation:post:output']],
    denormalizationContext: ['groups' => ['cultivation:post', 'cultivation:post:input']]
)]
#[Patch(
    normalizationContext  : ['groups' => ['cultivation:patch', 'cultivation:patch:output']],
    denormalizationContext: ['groups' => ['cultivation:patch', 'cultivation:patch:input']]
)]
#[Delete()]
#[Get(normalizationContext: ['groups' => ['cultivation:get']])]
#[GetCollection(
    normalizationContext: ['groups' => ['cultivation:collection']],
)]
#[ORM\HasLifecycleCallbacks]
class Cultivation extends AbstractIdOrmAndUlidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[Groups(['cultivation:get'])]
    #[ORM\Column(nullable: true)]
    #[ApiProperty(
        description: 'An integer indicating the minimal temperature for the sowing in degrees Celsius',
    )]
    private ?int $sowingMinimalTemperature = null;

    #[Groups(['cultivation:get'])]
    #[ORM\Column(nullable: true)]
    #[ApiProperty(
        description: 'An integer indicating the optimal temperature for the sowing in degrees Celsius',
    )]
    private ?int $sowingMaximalTemperature = null;

    #[Groups(['cultivation:get'])]
    #[ORM\Column(nullable: true)]
    #[ApiProperty(
        description: 'An integer indicating the spacing between two plants in millimeters',
    )]
    private ?int $plantingSpacing = null;

    #[Groups(['cultivation:get'])]
    #[ORM\Column(nullable: true)]
    #[ApiProperty(
        description: 'An integer indicating the minimal number of days to harvest',
    )]
    private ?int $minimalDaysToHarvest = null;

    #[Groups(['cultivation:get'])]
    #[ORM\Column(nullable: true)]
    #[ApiProperty(
        description: 'An integer indicating the maximal number of days to harvest',
    )]
    private ?int $maximalDaysToHarvest = null;

    #[Groups(['cultivation:get'])]
    #[ORM\Column(nullable: true)]
    #[ApiProperty(
        description: 'An integer indicating the temperature where the plant stop growing in degrees Celsius',
    )]
    private ?int $vegetationTemperatureThreshold = null;

    #[Groups(['cultivation:get'])]
    #[ORM\Column(type: Types::JSONB, nullable: true)]
    #[ApiProperty(
        description: 'An array of integer indicating the recommended sowing months',
    )]
    #[Assert\Choice(choices: Month::ALL, multiple: true)]
    private mixed $sowingMonths = null;

    #[Groups(['cultivation:get'])]
    #[ORM\Column(type: Types::JSONB, nullable: true)]
    #[ApiProperty(
        description: 'An array of integer indicating the expected harvesting months',
    )]
    #[Assert\Choice(choices: Month::ALL, multiple: true)]
    private mixed $harvestingMonths = null;

    #[ORM\OneToOne(mappedBy: 'cultivation', cascade: ['persist', 'remove'])]
    private ?Plant $plant = null;

    #[ORM\ManyToOne(inversedBy: 'cultivations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Maturity $maturity = null;

    #[ORM\ManyToOne(inversedBy: 'cultivations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exposure $exposure = null;

    #[Groups(['cultivation:get'])]
    #[ORM\Column(nullable: true)]
    #[ApiProperty(
        description: 'An integer indicating the minimal number of days to germination',
    )]
    private ?int $minimalDaysToGermination = null;

    #[Groups(['cultivation:get'])]
    #[ORM\Column(nullable: true)]
    #[ApiProperty(
        description: 'An integer indicating the maximal number of days to germination',
    )]
    private ?int $maximalDaysToGermination = null;

    public function getSowingMinimalTemperature(): ?int
    {
        return $this->sowingMinimalTemperature;
    }

    public function setSowingMinimalTemperature(?int $sowingMinimalTemperature): static
    {
        $this->sowingMinimalTemperature = $sowingMinimalTemperature;

        return $this;
    }

    public function getSowingMaximalTemperature(): ?int
    {
        return $this->sowingMaximalTemperature;
    }

    public function setSowingMaximalTemperature(?int $sowingMaximalTemperature): static
    {
        $this->sowingMaximalTemperature = $sowingMaximalTemperature;

        return $this;
    }

    public function getPlantingSpacing(): ?int
    {
        return $this->plantingSpacing;
    }

    public function setPlantingSpacing(?int $plantingSpacing): static
    {
        $this->plantingSpacing = $plantingSpacing;

        return $this;
    }

    public function getMinimalDaysToHarvest(): ?int
    {
        return $this->minimalDaysToHarvest;
    }

    public function setMinimalDaysToHarvest(?int $minimalDaysToHarvest): static
    {
        $this->minimalDaysToHarvest = $minimalDaysToHarvest;

        return $this;
    }

    public function getMaximalDaysToHarvest(): ?int
    {
        return $this->maximalDaysToHarvest;
    }

    public function setMaximalDaysToHarvest(?int $maximalDaysToHarvest): static
    {
        $this->maximalDaysToHarvest = $maximalDaysToHarvest;

        return $this;
    }

    public function getVegetationTemperatureThreshold(): ?int
    {
        return $this->vegetationTemperatureThreshold;
    }

    public function setVegetationTemperatureThreshold(?int $vegetationTemperatureThreshold): static
    {
        $this->vegetationTemperatureThreshold = $vegetationTemperatureThreshold;

        return $this;
    }

    public function getSowingMonths(): mixed
    {
        return $this->sowingMonths;
    }

    public function setSowingMonths(mixed $sowingMonths): static
    {
        $this->sowingMonths = $sowingMonths;

        return $this;
    }

    public function getHarvestingMonths(): mixed
    {
        return $this->harvestingMonths;
    }

    public function setHarvestingMonths(mixed $harvestingMonths): static
    {
        $this->harvestingMonths = $harvestingMonths;

        return $this;
    }

    #[Groups(['cultivation:get'])]
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(['cultivation:get'])]
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getPlant(): ?Plant
    {
        return $this->plant;
    }

    public function setPlant(Plant $plant): static
    {
        // set the owning side of the relation if necessary
        if ($plant->getCultivation() !== $this) {
            $plant->setCultivation($this);
        }

        $this->plant = $plant;

        return $this;
    }

    public function getMaturity(): ?Maturity
    {
        return $this->maturity;
    }

    public function setMaturity(?Maturity $maturity): static
    {
        $this->maturity = $maturity;

        return $this;
    }

    public function getExposure(): ?Exposure
    {
        return $this->exposure;
    }

    public function setExposure(?Exposure $exposure): static
    {
        $this->exposure = $exposure;

        return $this;
    }

    public function getMinimalDaysToGermination(): ?int
    {
        return $this->minimalDaysToGermination;
    }

    public function setMinimalDaysToGermination(?int $minimalDaysToGermination): static
    {
        $this->minimalDaysToGermination = $minimalDaysToGermination;

        return $this;
    }

    public function getMaximalDaysToGermination(): ?int
    {
        return $this->maximalDaysToGermination;
    }

    public function setMaximalDaysToGermination(?int $maximalDaysToGermination): static
    {
        $this->maximalDaysToGermination = $maximalDaysToGermination;

        return $this;
    }
}
