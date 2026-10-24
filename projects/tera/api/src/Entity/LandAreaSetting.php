<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use App\Repository\LandAreaSettingRepository;
use App\Security\Interface\LandAwareInterface;
use App\Security\Voter\LandAreaSettingVoter;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: LandAreaSettingRepository::class)]
#[ApiResource]
#[Patch(
    normalizationContext  : ['groups' => ['land_area_setting:patch', 'land_area_setting:patch:output']],
    denormalizationContext: ['groups' => ['land_area_setting:patch', 'land_area_setting:patch:input']],
    security              : "is_granted('".LandAreaSettingVoter::PATCH."', previous_object)")]
#[Get(
    normalizationContext: ['groups' => ['land_area_setting:get']],
    security            : "is_granted('".LandAreaSettingVoter::GET."', object)")]
class LandAreaSetting extends AbstractIdOrmAndUlidApiIdentified implements LandAwareInterface
{
    #[ORM\OneToOne(inversedBy: 'landAreaSetting', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['land_area_setting:get'])]
    private ?LandArea $landArea = null;

    #[ORM\Column]
    #[Groups(['land_area_setting:get', 'land_area_setting:patch'])]
    private ?bool $rotationActivated = false;

    #[Groups(['land_area_setting:patch:output', 'land_area_setting:get'])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
    }

    public function isRotationActivated(): ?bool
    {
        return $this->rotationActivated;
    }

    public function setRotationActivated(bool $rotationActivated): static
    {
        $this->rotationActivated = $rotationActivated;

        return $this;
    }

    public function getLand(): ?Land
    {
        return $this->getLandArea()->getLand();
    }

    public function getLandArea(): ?LandArea
    {
        return $this->landArea;
    }

    public function setLandArea(LandArea $landArea): static
    {
        $this->landArea = $landArea;

        return $this;
    }
}
