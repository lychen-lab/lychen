<?php

namespace App\Entity;

use App\Repository\SeedLibraryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUuidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;

/**
 * A seed library (grainothèque): a place where a community shares its seed lots.
 *
 * A plain Doctrine model, without any API Platform attribute: the API exposes it through
 * App\Api\Resource\SeedLibrary\SeedLibrary. The int $id inherited from
 * AbstractIdOrmAndUuidApiIdentified stays internal (primary and foreign keys); its $uuid
 * identifies the resource in the API.
 */
#[ORM\Entity(repositoryClass: SeedLibraryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class SeedLibrary extends AbstractIdOrmAndUuidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    /**
     * Inverse side of SeedLot::$seedLibrary: a lot joins a library through
     * SeedLot::setSeedLibrary(). Removing a library removes its lots.
     *
     * @var Collection<int, SeedLot>
     */
    #[ORM\OneToMany(targetEntity: SeedLot::class, mappedBy: 'seedLibrary', cascade: ['remove'])]
    private Collection $seedLots;

    public function __construct()
    {
        parent::__construct();
        $this->seedLots = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection<int, SeedLot>
     */
    public function getSeedLots(): Collection
    {
        return $this->seedLots;
    }
}
