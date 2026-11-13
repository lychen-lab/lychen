<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\SeedStockEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SeedStockEntryRepository::class)]
#[ApiResource]
#[GetCollection()]
#[Post()]
#[Patch()]
#[Delete()]
#[Get()]
#[ORM\HasLifecycleCallbacks]
class SeedStockEntry extends AbstractIdOrmAndUlidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\ManyToOne(inversedBy: 'seedStockEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SeedStock $seedStock = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantityInGram = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantityInNumberOfSeed = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $purchaseDate = null;

    #[ORM\Column]
    private ?bool $publiclyShared = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $variety = null;

    /**
     * The flora seed lot this entry was received from, when a seed library gave it (see
     * SeedLotGivenHandler). Unique: a lot is received once.
     */
    #[ORM\Column(type: UuidType::NAME, unique: true, nullable: true)]
    private ?Uuid $receivedFromSeedLot = null;

    public function getSeedStock(): ?SeedStock
    {
        return $this->seedStock;
    }

    public function setSeedStock(?SeedStock $seedStock): static
    {
        $this->seedStock = $seedStock;

        return $this;
    }

    public function getQuantityInGram(): ?int
    {
        return $this->quantityInGram;
    }

    public function setQuantityInGram(?int $quantityInGram): static
    {
        $this->quantityInGram = $quantityInGram;

        return $this;
    }

    public function getQuantityInNumberOfSeed(): ?int
    {
        return $this->quantityInNumberOfSeed;
    }

    public function setQuantityInNumberOfSeed(?int $quantityInNumberOfSeed): static
    {
        $this->quantityInNumberOfSeed = $quantityInNumberOfSeed;

        return $this;
    }

    public function getPurchaseDate(): ?\DateTimeInterface
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(?\DateTimeInterface $purchaseDate): static
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    public function isPubliclyShared(): ?bool
    {
        return $this->publiclyShared;
    }

    public function setPubliclyShared(bool $publiclyShared): static
    {
        $this->publiclyShared = $publiclyShared;

        return $this;
    }

    public function getVariety(): ?string
    {
        return $this->variety;
    }

    public function setVariety(?string $variety): static
    {
        $this->variety = $variety;

        return $this;
    }

    public function getReceivedFromSeedLot(): ?Uuid
    {
        return $this->receivedFromSeedLot;
    }

    public function setReceivedFromSeedLot(?Uuid $receivedFromSeedLot): static
    {
        $this->receivedFromSeedLot = $receivedFromSeedLot;

        return $this;
    }
}
