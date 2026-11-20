<?php

namespace App\Entity;

use App\Repository\AreaActivityRepository;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUuidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;

#[ORM\Entity(repositoryClass: AreaActivityRepository::class)]
#[ORM\HasLifecycleCallbacks]
class AreaActivity extends AbstractIdOrmAndUuidApiIdentified
{
    use CreatedAtTrait;

    #[ORM\Column(length: 255, unique: true)]
    private string $code;

    public function getCode(): ?string
    {
        return $this->code ?? null;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }
}
