<?php

namespace App\Entity;

use App\Repository\PersonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilZitadelBundle\Abstract\AbstractZitadelUser;

#[ORM\Entity(repositoryClass: PersonRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_AUTH_ID', fields: ['authId'])]
#[ORM\HasLifecycleCallbacks]
class Person extends AbstractZitadelUser
{
    /**
     * @var Collection<int, AreaRequest>
     */
    #[ORM\OneToMany(targetEntity: AreaRequest::class, mappedBy: 'requester', orphanRemoval: true)]
    private Collection $areaRequests;

    public function __construct()
    {
        $this->areaRequests = new ArrayCollection();
    }

    /**
     * @return Collection<int, AreaRequest>
     */
    public function getAreaRequests(): Collection
    {
        return $this->areaRequests;
    }

    public function addAreaRequest(AreaRequest $areaRequest): static
    {
        if (!$this->areaRequests->contains($areaRequest)) {
            $this->areaRequests->add($areaRequest);
            $areaRequest->setRequester($this);
        }

        return $this;
    }

    public function removeAreaRequest(AreaRequest $areaRequest): static
    {
        if ($this->areaRequests->removeElement($areaRequest)) {
            if ($areaRequest->getRequester() === $this) {
                $areaRequest->setRequester(null);
            }
        }

        return $this;
    }
}
