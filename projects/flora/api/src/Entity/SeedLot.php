<?php

namespace App\Entity;

use App\Repository\SeedLotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUuidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;

/**
 * A lot of seeds of one variety, kept by a seed library.
 *
 * Owning side of both its relations: a lot belongs to one SeedLibrary (ManyToOne) and
 * carries any number of Characteristic (ManyToMany).
 */
#[ORM\Entity(repositoryClass: SeedLotRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SeedLot extends AbstractIdOrmAndUuidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Column(length: 255)]
    private string $variety;

    #[ORM\Column(nullable: true)]
    private ?int $harvestYear = null;

    #[ORM\Column]
    private int $quantityInGrams = 0;

    #[ORM\ManyToOne(inversedBy: 'seedLots')]
    #[ORM\JoinColumn(nullable: false)]
    private SeedLibrary $seedLibrary;

    /**
     * @var Collection<int, Characteristic>
     */
    #[ORM\ManyToMany(targetEntity: Characteristic::class)]
    private Collection $characteristics;

    public function __construct()
    {
        parent::__construct();
        $this->characteristics = new ArrayCollection();
    }

    public function getVariety(): string
    {
        return $this->variety;
    }

    public function setVariety(string $variety): static
    {
        $this->variety = $variety;

        return $this;
    }

    public function getHarvestYear(): ?int
    {
        return $this->harvestYear;
    }

    public function setHarvestYear(?int $harvestYear): static
    {
        $this->harvestYear = $harvestYear;

        return $this;
    }

    public function getQuantityInGrams(): int
    {
        return $this->quantityInGrams;
    }

    public function setQuantityInGrams(int $quantityInGrams): static
    {
        $this->quantityInGrams = $quantityInGrams;

        return $this;
    }

    public function getSeedLibrary(): SeedLibrary
    {
        return $this->seedLibrary;
    }

    public function setSeedLibrary(SeedLibrary $seedLibrary): static
    {
        $this->seedLibrary = $seedLibrary;

        return $this;
    }

    /**
     * @return Collection<int, Characteristic>
     */
    public function getCharacteristics(): Collection
    {
        return $this->characteristics;
    }

    /**
     * With removeCharacteristic(), lets the object mapper (through the PropertyAccess
     * component) sync the collection with a list: it adds the missing items and removes
     * the others.
     */
    public function addCharacteristic(Characteristic $characteristic): static
    {
        if (!$this->characteristics->contains($characteristic)) {
            $this->characteristics->add($characteristic);
        }

        return $this;
    }

    public function removeCharacteristic(Characteristic $characteristic): static
    {
        $this->characteristics->removeElement($characteristic);

        return $this;
    }
}
