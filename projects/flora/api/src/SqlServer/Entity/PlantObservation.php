<?php

namespace App\SqlServer\Entity;

use App\SqlServer\Repository\PlantObservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUuidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;

/**
 * A plant seen in the field, stored on SQL Server.
 *
 * The `sqlserver` entity manager maps it, never the default (Postgres) one: that one
 * only reads src/Entity, this one only src/SqlServer/Entity (config/packages/doctrine.php).
 * Read and write it through its repository, or `$doctrine->getManager('sqlserver')`;
 * a plain `EntityManagerInterface` is the default one. Its table comes from the
 * migrations of migrations/SqlServer (config/migrations/sqlserver.php).
 *
 * Like the Postgres entities, an int id for the database and a uuid for the API: on
 * SQL Server the uuid is a native UNIQUEIDENTIFIER.
 */
#[ORM\Entity(repositoryClass: PlantObservationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PlantObservation extends AbstractIdOrmAndUuidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    /** Scientific name, e.g. "Lavandula angustifolia". */
    #[ORM\Column(length: 255)]
    private string $species;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $observedAt;

    /** WGS 84 degrees, with the longitude: where the plant was seen, when known. */
    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    /**
     * DBAL makes a text column a VARCHAR(MAX), which the server's default collation
     * limits to Latin-1 (anything else becomes "?"). A UTF-8 collation keeps all of
     * Unicode; strings are NVARCHAR, Unicode already.
     */
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['collation' => 'Latin1_General_100_CI_AS_SC_UTF8'])]
    private ?string $notes = null;

    public function getSpecies(): string
    {
        return $this->species;
    }

    public function setSpecies(string $species): static
    {
        $this->species = $species;

        return $this;
    }

    public function getObservedAt(): \DateTimeImmutable
    {
        return $this->observedAt;
    }

    public function setObservedAt(\DateTimeImmutable $observedAt): static
    {
        $this->observedAt = $observedAt;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }
}
