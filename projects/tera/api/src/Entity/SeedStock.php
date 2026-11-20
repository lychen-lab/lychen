<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\SeedStockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;

#[ORM\Entity(repositoryClass: SeedStockRepository::class)]
#[ApiResource]
#[GetCollection()]
#[Post()]
#[Patch(security: 'object.person == user')]
#[Delete(security: 'object.person == user')]
#[Get(security: 'object.person == user')]
#[ORM\HasLifecycleCallbacks]
class SeedStock extends AbstractIdOrmAndUlidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\ManyToOne(inversedBy: 'seedStocks')]
    #[ORM\JoinColumn(nullable: false)]
    private Person $person;

    /**
     * @var Collection<int, SeedStockEntry>
     */
    #[ORM\OneToMany(targetEntity: SeedStockEntry::class, mappedBy: 'seedStock', orphanRemoval: true)]
    private Collection $seedStockEntries;

    public function __construct()
    {
        parent::__construct();
        $this->seedStockEntries = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name ?? null;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person ?? null;
    }

    public function setPerson(?Person $person): static
    {
        // Null (from Person::removeSeedStock()) leaves it unset, as Doctrine does for a NOT NULL column.
        if (null === $person) {
            unset($this->person);
        } else {
            $this->person = $person;
        }

        return $this;
    }

    /**
     * @return Collection<int, SeedStockEntry>
     */
    public function getSeedStockEntries(): Collection
    {
        return $this->seedStockEntries;
    }

    public function addSeedStockEntry(SeedStockEntry $seedStockEntry): static
    {
        if (!$this->seedStockEntries->contains($seedStockEntry)) {
            $this->seedStockEntries->add($seedStockEntry);
            $seedStockEntry->setSeedStock($this);
        }

        return $this;
    }

    public function removeSeedStockEntry(SeedStockEntry $seedStockEntry): static
    {
        if ($this->seedStockEntries->removeElement($seedStockEntry)) {
            // set the owning side to null (unless already changed)
            if ($seedStockEntry->getSeedStock() === $this) {
                $seedStockEntry->setSeedStock(null);
            }
        }

        return $this;
    }
}
