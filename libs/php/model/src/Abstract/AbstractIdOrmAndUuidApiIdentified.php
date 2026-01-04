<?php

namespace Lychen\UtilModel\Abstract;

use ApiPlatform\Metadata\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Interface\IdIdentifiedInterface;
use Lychen\UtilModel\Interface\UuidIdentifiedInterface;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * Create default $id(int) as ORM\Id and $uuid(uuid) as api identifier. <br/>
 * Possibility to give Uuid through constructor,if null it's auto-generated.
 */
#[ORM\MappedSuperclass]
abstract class AbstractIdOrmAndUuidApiIdentified implements UuidIdentifiedInterface, IdIdentifiedInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[ORM\Column(type: UuidType::NAME, unique: true)]
    protected Uuid $uuid;

    public function __construct(?Uuid $uuid = null)
    {
        $this->uuid = $uuid ?: Uuid::v7();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}
