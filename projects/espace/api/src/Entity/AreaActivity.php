<?php

namespace App\Entity;

use App\Repository\AreaActivityRepository;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUuidApiIdentified;

#[ORM\Entity(repositoryClass: AreaActivityRepository::class)]
class AreaActivity extends AbstractIdOrmAndUuidApiIdentified
{
    #[ORM\Column(length: 255)]
    private ?string $code = null;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }
}
