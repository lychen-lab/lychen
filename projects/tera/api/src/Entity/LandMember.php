<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\QueryParameter;
use App\Filter\LandFilter;
use App\Provider\LandMembersMeProvider;
use App\Repository\LandMemberRepository;
use App\Security\Interface\LandAwareInterface;
use App\Security\Interface\PermissionHolder;
use App\Security\Voter\LandMemberVoter;
use App\Validator\LandRolesBelongToLand;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LandMemberRepository::class)]
#[ApiResource()]
#[Patch(
    normalizationContext  : ['groups' => ['land_member:patch', 'land_member:patch:output']],
    denormalizationContext: ['groups' => ['land_member:patch', 'land_member:patch:input']],
    security              : "is_granted('".LandMemberVoter::PATCH."', previous_object)")
]
#[Delete(security: "is_granted('".LandMemberVoter::DELETE."', object)")]
#[Get(
    uriTemplate         : '/land_members/{ulid}',
    requirements        : ['ulid' => '[0-9A-HJKMNP-TV-Z]{26}'],
    normalizationContext: ['groups' => ['land_member:get']],
    security            : "is_granted('".LandMemberVoter::GET."', object)"
)]
#[GetCollection(
    normalizationContext: ['groups' => ['land_member:collection']],
    security            : "is_granted('".LandMemberVoter::COLLECTION."')",
    parameters          : [
        new QueryParameter(
            key   : 'land',
            filter: LandFilter::class,
        ),
    ])]
#[Get(
    uriTemplate         : '/land_members/me',
    normalizationContext: ['groups' => ['land_member:me']],
    security            : "is_granted('".LandMemberVoter::ME."')",
    priority            : 10,
    name                : 'land-member_me',
    provider            : LandMembersMeProvider::class,
    parameters          : [
        new QueryParameter(
            key   : 'land',
            filter: LandFilter::class,
        ),
    ])]
#[ORM\HasLifecycleCallbacks]
#[LandRolesBelongToLand]
class LandMember extends AbstractIdOrmAndUlidApiIdentified implements LandAwareInterface, PermissionHolder
{
    #[ORM\Column]
    #[Groups(['land_member:collection', 'land_member:get', 'land_member:patch:output'])]
    private ?\DateTimeImmutable $joinedAt = null;

    #[ORM\Column]
    #[Groups(['land_member:collection', 'land_member:get', 'land_member:me', 'land_member:patch:output'])]
    private ?bool $owner = false;

    #[ORM\ManyToOne(inversedBy: 'landMembers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['land_member:me', 'land_member:patch:output'])]
    private ?Land $land = null;

    #[ORM\ManyToOne(inversedBy: 'landMembers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['land_member:collection', 'land_member:get', 'land_member:patch:output'])]
    private ?Person $person = null;

    #[ORM\OneToOne(mappedBy: 'landMember', cascade: ['persist', 'remove'])]
    #[Groups(['land_member:collection', 'land_member:get', 'land_member:patch:output'])]
    private ?LandMemberSetting $landMemberSetting = null;

    /**
     * @var Collection<int, LandRole>
     */
    #[ORM\ManyToMany(targetEntity: LandRole::class, inversedBy: 'landMembers')]
    #[Groups(['land_member:collection',
        'land_member:get',
        'land_member:patch',
        'land_member:me'])]
    #[Assert\Valid()]
    private Collection $landRoles;

    public function __construct(?Ulid $ulid = null)
    {
        parent::__construct($ulid);
        $this->setLandMemberSetting(new LandMemberSetting());
        $this->landRoles = new ArrayCollection();
    }

    #[Groups(['land_member:collection',
        'land_member:get',
        'land_member:patch:output',
        'land_member:me'])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
    }

    public function getJoinedAt(): ?\DateTimeImmutable
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(\DateTimeImmutable $joinedAt): static
    {
        $this->joinedAt = $joinedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setJoinedAtValue(): void
    {
        $this->joinedAt = new \DateTimeImmutable();
    }

    public function isOwner(): ?bool
    {
        return $this->owner;
    }

    public function setOwner(bool $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getLand(): ?Land
    {
        return $this->land;
    }

    public function setLand(?Land $land): static
    {
        $this->land = $land;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): static
    {
        $this->person = $person;

        return $this;
    }

    public function getLandMemberSetting(): ?LandMemberSetting
    {
        return $this->landMemberSetting;
    }

    public function setLandMemberSetting(LandMemberSetting $landMemberSetting): static
    {
        // set the owning side of the relation if necessary
        if ($landMemberSetting->getLandMember() !== $this) {
            $landMemberSetting->setLandMember($this);
        }

        $this->landMemberSetting = $landMemberSetting;

        return $this;
    }

    public function removeLandRole(LandRole $landRole): static
    {
        $this->landRoles->removeElement($landRole);

        return $this;
    }

    public function getPermissions(): array
    {
        $roles = $this->getLandRoles();
        $effectiveRights = [];
        foreach ($roles as $role) {
            if ($role->getPermissions()) {
                $effectiveRights = array_merge($effectiveRights, $role->getPermissions());
            }
        }

        return array_unique($effectiveRights);
    }

    /**
     * @return Collection<int, LandRole>
     */
    public function getLandRoles(): Collection
    {
        return $this->landRoles;
    }

    public function setLandRoles(Collection $landRoles): static
    {
        foreach ($landRoles as $landRole) {
            $this->addLandRole($landRole);
        }

        return $this;
    }

    public function addLandRole(LandRole $landRole): static
    {
        if (!$this->landRoles->contains($landRole)) {
            $this->landRoles->add($landRole);
        }

        return $this;
    }
}
