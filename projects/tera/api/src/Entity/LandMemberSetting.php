<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use App\Repository\LandMemberSettingRepository;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: LandMemberSettingRepository::class)]
#[ApiResource()]
#[Patch(
    normalizationContext  : ['groups' => ['land_member_setting:patch', 'land_member_setting:patch:output']],
    denormalizationContext: ['groups' => ['land_member_setting:patch', 'land_member_setting:patch:input']],
    security              : 'object.getLandMember().getPerson() == user'
)]
#[Get(
    normalizationContext: ['groups' => ['land_member_setting:get']],
    security            : 'object.getLandMember().getPerson() == user'
)]
class LandMemberSetting extends AbstractIdOrmAndUlidApiIdentified
{
    #[ORM\OneToOne(inversedBy: 'landMemberSetting', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?LandMember $landMember = null;

    #[ORM\Column]
    #[Groups(['land_member_setting:get', 'land_member_setting:patch'])]
    private ?bool $emailNotificationActivated = false;

    #[Groups(['land_member_setting:get', 'land_member_setting:patch:output'])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
    }

    public function isEmailNotificationActivated(): ?bool
    {
        return $this->emailNotificationActivated;
    }

    public function setEmailNotificationActivated(bool $emailNotificationActivated): static
    {
        $this->emailNotificationActivated = $emailNotificationActivated;

        return $this;
    }

    public function getLandMember(): ?LandMember
    {
        return $this->landMember;
    }

    public function setLandMember(LandMember $landMember): static
    {
        $this->landMember = $landMember;

        return $this;
    }
}
