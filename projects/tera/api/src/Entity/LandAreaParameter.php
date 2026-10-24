<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use App\Repository\LandAreaParameterRepository;
use App\Security\Interface\LandAwareInterface;
use App\Security\Voter\LandAreaParameterVoter;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: LandAreaParameterRepository::class)]
#[ApiResource]
#[Patch(
    normalizationContext  : ['groups' => ['land_area_parameter:patch', 'land_area_parameter:patch:output']],
    denormalizationContext: ['groups' => ['land_area_parameter:patch', 'land_area_parameter:patch:input']],
    security              : "is_granted('".LandAreaParameterVoter::PATCH."', previous_object)"
)]
#[Get(
    normalizationContext: ['groups' => ['land_area_parameter:get']],
    security            : "is_granted('".LandAreaParameterVoter::GET."', object)"
)]
class LandAreaParameter extends AbstractIdOrmAndUlidApiIdentified implements LandAwareInterface
{
    #[ORM\OneToOne(inversedBy: 'landAreaParameter', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['land_area_parameter:get'])]
    private ?LandArea $landArea = null;

    #[ORM\Column]
    #[Groups(['land_area_parameter:patch', 'land_area_parameter:get'])]
    private ?bool $aboveGround = false;

    #[ORM\Column(nullable: true)]
    #[Groups(['land_area_parameter:patch', 'land_area_parameter:get'])]
    private ?int $width = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['land_area_parameter:patch', 'land_area_parameter:get'])]
    private ?int $length = null;

    #[Groups(['land_area_parameter:patch:output', 'land_area_parameter:get'])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
    }

    public function isAboveGround(): ?bool
    {
        return $this->aboveGround;
    }

    public function setAboveGround(bool $aboveGround): static
    {
        $this->aboveGround = $aboveGround;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(?int $length): static
    {
        $this->length = $length;

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
