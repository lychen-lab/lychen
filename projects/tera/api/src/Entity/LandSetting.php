<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use App\Repository\LandSettingRepository;
use App\Security\Interface\LandAwareInterface;
use App\Security\Voter\LandSettingVoter;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: LandSettingRepository::class)]
#[ApiResource()]
#[Patch(
    normalizationContext  : ['groups' => ['land_setting:patch', 'land_setting:patch:output']],
    denormalizationContext: ['groups' => ['land_setting:patch', 'land_setting:patch:input']],
    security              : "is_granted('".LandSettingVoter::PATCH."', previous_object)"
)]
#[Get(
    normalizationContext: ['groups' => ['land_setting:get']],
    security            : "is_granted('".LandSettingVoter::GET."', object)"
)]
class LandSetting extends AbstractIdOrmAndUlidApiIdentified implements LandAwareInterface
{
    #[ORM\OneToOne(inversedBy: 'landSetting', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['land_setting:get', 'land_setting:patch:output'])]
    private ?Land $land = null;

    #[ORM\Column]
    #[Groups(['land_setting:get', 'land_setting:patch'])]
    private ?bool $lookingForMember = false;

    #[Groups(['land_setting:patch:output', 'land_setting:get'])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
    }

    public function getLand(): ?Land
    {
        return $this->land;
    }

    public function setLand(Land $land): static
    {
        $this->land = $land;

        return $this;
    }

    public function isLookingForMember(): ?bool
    {
        return $this->lookingForMember;
    }

    public function setLookingForMember(bool $lookingForMember): static
    {
        $this->lookingForMember = $lookingForMember;

        return $this;
    }
}
