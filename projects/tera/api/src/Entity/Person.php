<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use App\Security\Interface\PermissionHolder;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilZitadelBundle\Abstract\AbstractZitadelUser;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_AUTH_ID', fields: ['authId'])]
#[ORM\HasLifecycleCallbacks]
class Person extends AbstractZitadelUser implements PermissionHolder
{
    /**
     * @var Collection<int, LandMember>
     */
    #[ORM\OneToMany(targetEntity: LandMember::class, mappedBy: 'person', orphanRemoval: true)]
    private Collection $landMembers;

    /**
     * @var Collection<int, LandRequest>
     */
    #[ORM\OneToMany(targetEntity: LandRequest::class, mappedBy: 'person', orphanRemoval: true)]
    private Collection $landRequests;

    /**
     * @var Collection<int, SeedStock>
     */
    #[ORM\OneToMany(targetEntity: SeedStock::class, mappedBy: 'person', orphanRemoval: true)]
    private Collection $seedStocks;

    /**
     * @var Collection<int, LandMemberInvitation>
     */
    #[ORM\OneToMany(targetEntity: LandMemberInvitation::class, mappedBy: 'person')]
    private Collection $landMemberInvitations;

    /**
     * @var Collection<int, LandDeal>
     */
    #[ORM\OneToMany(targetEntity: LandDeal::class, mappedBy: 'person')]
    private Collection $landDeals;

    private array $permissions = [];

    /**
     * @var Collection<int, PersonApiKey>
     */
    #[ORM\OneToMany(targetEntity: PersonApiKey::class, mappedBy: 'person', orphanRemoval: true)]
    private Collection $personApiKeys;

    public function __construct()
    {
        $this->landMembers = new ArrayCollection();
        $this->landRequests = new ArrayCollection();
        $this->seedStocks = new ArrayCollection();
        $this->landMemberInvitations = new ArrayCollection();
        $this->landDeals = new ArrayCollection();
        $this->personApiKeys = new ArrayCollection();
    }

    #[Groups(['land_member:collection'])]
    public function getGivenName(): ?string
    {
        return parent::getGivenName();
    }

    #[Groups(['land_member:collection'])]
    public function getFamilyName(): ?string
    {
        return parent::getFamilyName();
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
            $landMember->setPerson($this);
        }

        return $this;
    }

    public function removeLandMember(LandMember $landMember): static
    {
        if ($this->landMembers->removeElement($landMember)) {
            // set the owning side to null (unless already changed)
            if ($landMember->getPerson() === $this) {
                $landMember->setPerson(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LandRequest>
     */
    public function getLandRequests(): Collection
    {
        return $this->landRequests;
    }

    public function addLandRequest(LandRequest $landRequest): static
    {
        if (!$this->landRequests->contains($landRequest)) {
            $this->landRequests->add($landRequest);
            $landRequest->setPerson($this);
        }

        return $this;
    }

    public function removeLandRequest(LandRequest $landRequest): static
    {
        if ($this->landRequests->removeElement($landRequest)) {
            // set the owning side to null (unless already changed)
            if ($landRequest->getPerson() === $this) {
                $landRequest->setPerson(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SeedStock>
     */
    public function getSeedStocks(): Collection
    {
        return $this->seedStocks;
    }

    public function addSeedStock(SeedStock $seedStock): static
    {
        if (!$this->seedStocks->contains($seedStock)) {
            $this->seedStocks->add($seedStock);
            $seedStock->setPerson($this);
        }

        return $this;
    }

    public function removeSeedStock(SeedStock $seedStock): static
    {
        if ($this->seedStocks->removeElement($seedStock)) {
            // set the owning side to null (unless already changed)
            if ($seedStock->getPerson() === $this) {
                $seedStock->setPerson(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LandMemberInvitation>
     */
    public function getLandMemberInvitations(): Collection
    {
        return $this->landMemberInvitations;
    }

    public function addLandMemberInvitation(LandMemberInvitation $landMemberInvitation): static
    {
        if (!$this->landMemberInvitations->contains($landMemberInvitation)) {
            $this->landMemberInvitations->add($landMemberInvitation);
            $landMemberInvitation->setPerson($this);
        }

        return $this;
    }

    public function removeLandMemberInvitation(LandMemberInvitation $landMemberInvitation): static
    {
        if ($this->landMemberInvitations->removeElement($landMemberInvitation)) {
            // set the owning side to null (unless already changed)
            if ($landMemberInvitation->getPerson() === $this) {
                $landMemberInvitation->setPerson(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LandDeal>
     */
    public function getLandDeals(): Collection
    {
        return $this->landDeals;
    }

    public function addLandDeal(LandDeal $landDeal): static
    {
        if (!$this->landDeals->contains($landDeal)) {
            $this->landDeals->add($landDeal);
            $landDeal->setPerson($this);
        }

        return $this;
    }

    public function removeLandDeal(LandDeal $landDeal): static
    {
        if ($this->landDeals->removeElement($landDeal)) {
            // set the owning side to null (unless already changed)
            if ($landDeal->getPerson() === $this) {
                $landDeal->setPerson(null);
            }
        }

        return $this;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function setPermissions(?array $permissions): static
    {
        $this->permissions = $permissions;

        return $this;
    }

    /**
     * @return Collection<int, PersonApiKey>
     */
    public function getPersonApiKeys(): Collection
    {
        return $this->personApiKeys;
    }

    public function addPersonApiKey(PersonApiKey $personApiKey): static
    {
        if (!$this->personApiKeys->contains($personApiKey)) {
            $this->personApiKeys->add($personApiKey);
            $personApiKey->setPerson($this);
        }

        return $this;
    }

    public function removePersonApiKey(PersonApiKey $personApiKey): static
    {
        if ($this->personApiKeys->removeElement($personApiKey)) {
            // set the owning side to null (unless already changed)
            if ($personApiKey->getPerson() === $this) {
                $personApiKey->setPerson(null);
            }
        }

        return $this;
    }
}
