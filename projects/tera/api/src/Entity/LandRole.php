<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Filter\LandFilter;
use App\Repository\LandRoleRepository;
use App\Security\Constant\LandMemberPermission;
use App\Security\Interface\LandAwareInterface;
use App\Security\Voter\LandRoleVoter;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Lychen\UtilModel\Trait\PositionTrait;
use Lychen\UtilModel\Trait\UpdatedAtTrait;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LandRoleRepository::class)]
#[ApiResource]
#[Post(
    normalizationContext   : ['groups' => ['land_role:post', 'land_role:post:output']],
    denormalizationContext : ['groups' => ['land_role:post', 'land_role:post:input']],
    securityPostDenormalize: "is_granted('" . LandRoleVoter::POST . "', object)"
)]
#[Patch(
    normalizationContext  : ['groups' => ['land_role:patch', 'land_role:patch:output']],
    denormalizationContext: ['groups' => ['land_role:patch', 'land_role:patch:input']],
    security              : "is_granted('" . LandRoleVoter::PATCH . "', previous_object)"
)]
#[Delete(
    security: "is_granted('" . LandRoleVoter::DELETE . "', object)"
)]
#[Get(
    normalizationContext: ['groups' => ['land_role:get']], security: "is_granted('" . LandRoleVoter::GET . "', object)"
)]
#[GetCollection(
    normalizationContext: ['groups' => ['land_role:collection']],
    security            : "is_granted('" . LandRoleVoter::COLLECTION . "')",
    parameters          : [
        new QueryParameter(
            key   : 'land',
            filter: LandFilter::class,
        ),
        'order[:property]' => new QueryParameter(filter: 'land_role.order_filter'),
    ]
)]
#[ORM\HasLifecycleCallbacks]
class LandRole extends AbstractIdOrmAndUlidApiIdentified implements LandAwareInterface
{
    use CreatedAtTrait;
    use UpdatedAtTrait;
    use PositionTrait;

    #[ORM\Column(length: 255)]
    #[Groups(["land_role:collection",
              "land_member:collection",
              "land_member_invitation:collection",
              "land_role:get",
              "land_role:patch",
              "land_member:me",
              "land_role:post",
              "land_member_invitation:collection-by-email"])]
    #[Assert\NotBlank()]
    private ?string $name = null;

    #[Gedmo\SortableGroup()]
    #[ORM\ManyToOne(inversedBy: 'landRoles')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["land_role:get", "land_role:post"])]
    private ?Land $land = null;

    /**
     * @var Collection<int, LandMember>
     */
    #[ORM\ManyToMany(targetEntity: LandMember::class, mappedBy: 'landRoles')]
    #[Groups(["land_role:collection", "land_role:get"])]
    private Collection $landMembers;

    #[ORM\Column(nullable: true, options: ['jsonb' => true])]
    #[Assert\Choice(choices: LandMemberPermission::ALL, multiple: true)]
    #[Groups(["land_role:collection", "land_role:get", "land_role:patch", "land_role:post", "land_member:me"])]
    #[ApiProperty(openapiContext: [
        'type' => 'array',
        'enum' => LandMemberPermission::ALL,
        'example' => LandMemberPermission::ALL
    ])]
    private ?array $permissions = null;

    public function __construct()
    {
        parent::__construct();
        $this->landMembers = new ArrayCollection();
    }

    #[Groups(["land_role:collection", "land_role:get", "land_role:patch:output", "land_role:post:output"])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
    }

    #[Groups(["land_role:collection", "land_role:get", "land_role:patch:output", "land_role:post:output"])]
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(["land_role:collection", "land_role:get", "land_role:patch:output", "land_role:post:output"])]
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    /**
     * @return Collection<int, LandMember>
     */
    public function getLandMembers(): Collection
    {
        return $this->landMembers;
    }

    public function addLandMember(LandMember $landMember): static
    {
        if (!$this->landMembers->contains($landMember)) {
            $this->landMembers->add($landMember);
            $landMember->addLandRole($this);
        }

        return $this;
    }

    public function removeLandMember(LandMember $landMember): static
    {
        if ($this->landMembers->removeElement($landMember)) {
            $landMember->removeLandRole($this);
        }

        return $this;
    }

    public function getPermissions(): ?array
    {
        return $this->permissions;
    }

    public function setPermissions(?array $permissions): static
    {
        $this->permissions = $permissions;

        return $this;
    }

    #[Groups(["land_role:collection", "land_role:get"])]
    public function getPosition(): int
    {
        return $this->position;
    }
}
