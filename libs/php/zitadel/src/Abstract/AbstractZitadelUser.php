<?php

namespace Lychen\UtilZitadelBundle\Abstract;

use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilZitadelBundle\Interface\AuthIdIdentifiedInterface;
use Lychen\UtilZitadelBundle\Interface\HasEmailInterface;
use Lychen\UtilZitadelBundle\Interface\HasRolesInterface;
use Lychen\UtilZitadelBundle\Interface\NamedEntityInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\MappedSuperclass]
class AbstractZitadelUser implements AuthIdIdentifiedInterface, UserInterface, NamedEntityInterface, HasEmailInterface, HasRolesInterface
{
    #[ORM\Column(length: 180)]
    protected ?string $authId = null;
    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    protected array $roles = [];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $givenName = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $familyName = null;

    public function getAuthId(): ?string
    {
        return $this->authId;
    }

    public function setAuthId(string $authId): static
    {
        $this->authId = $authId;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->authId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    public function setGivenName(?string $givenName): static
    {
        $this->givenName = $givenName;

        return $this;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(?string $familyName): static
    {
        $this->familyName = $familyName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }
}
