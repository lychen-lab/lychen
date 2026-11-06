<?php

namespace App\Entity;

use App\Repository\CharacteristicRepository;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUuidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * A characteristic a seed lot can carry (early, heirloom, drought tolerant...).
 *
 * The SeedLot -> Characteristic ManyToMany is unidirectional: a characteristic does
 * not list its lots. Its join table rows go away with it (ON DELETE CASCADE).
 *
 * UniqueEntity is checked on the entity, once the object mapper has mapped the input
 * DTO onto it: API Platform validates the DTO first, then the mapped entity.
 */
#[ORM\Entity(repositoryClass: CharacteristicRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('code')]
class Characteristic extends AbstractIdOrmAndUuidApiIdentified
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Column(length: 100, unique: true)]
    private string $code;

    #[ORM\Column(length: 255)]
    private string $label;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }
}
