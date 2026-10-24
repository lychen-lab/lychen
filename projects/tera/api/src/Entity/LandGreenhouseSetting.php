<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use App\Repository\LandGreenhouseSettingRepository;
use App\Security\Interface\LandAwareInterface;
use App\Security\Voter\LandGreenhouseSettingVoter;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: LandGreenhouseSettingRepository::class)]
#[ApiResource]
#[Patch(
    normalizationContext  : ['groups' => ['land_greenhouse_setting:patch', 'land_greenhouse_setting:patch:output']],
    denormalizationContext: ['groups' => ['land_greenhouse_setting:patch', 'land_greenhouse_setting:patch:input']],
    security              : "is_granted('".LandGreenhouseSettingVoter::PATCH."', previous_object)"
)]
#[Get(
    normalizationContext: ['groups' => ['land_greenhouse_setting:get']],
    security            : "is_granted('".LandGreenhouseSettingVoter::GET."', object)"
)]
class LandGreenhouseSetting extends AbstractIdOrmAndUlidApiIdentified implements LandAwareInterface
{
    #[ORM\OneToOne(inversedBy: 'landGreenhouseSetting', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['land_greenhouse_setting:get'])]
    private ?LandGreenhouse $landGreenhouse = null;

    #[Groups(['land_greenhouse_setting:patch:output', 'land_greenhouse_setting:get'])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
    }

    public function getLand(): ?Land
    {
        return $this->getLandGreenhouse()->getLand();
    }

    public function getLandGreenhouse(): ?LandGreenhouse
    {
        return $this->landGreenhouse;
    }

    public function setLandGreenhouse(LandGreenhouse $landGreenhouse): static
    {
        $this->landGreenhouse = $landGreenhouse;

        return $this;
    }
}
