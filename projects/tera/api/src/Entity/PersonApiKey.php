<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\PersonApiKeyRepository;
use App\Security\Constant\PersonPermission;
use App\Security\Interface\PermissionHolder;
use App\Security\JWT\JWTPayloadable;
use App\Security\Voter\PersonApiKeyVoter;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PersonApiKeyRepository::class)]
#[ApiResource]
#[Post(
    normalizationContext  : ['groups' => ['person_api_key:post', 'person_api_key:post:output']],
    denormalizationContext: ['groups' => ['person_api_key:post', 'person_api_key:post:input']],
    security              : "is_granted('" . PersonApiKeyVoter::POST . "')"
)]
#[Delete(
    security: "is_granted('" . PersonApiKeyVoter::DELETE . "', object)"
)]
#[Get(
    normalizationContext: ['groups' => ['person_api_key:get']],
    security            : "is_granted('" . PersonApiKeyVoter::GET . "', object)"
)]
#[GetCollection(
    normalizationContext: ['groups' => ['person_api_key:collection']],
    security            : "is_granted('" . PersonApiKeyVoter::COLLECTION . "')"
)]
#[ORM\HasLifecycleCallbacks]
class PersonApiKey extends AbstractIdOrmAndUlidApiIdentified implements PermissionHolder, UserInterface, JWTPayloadable
{
    use CreatedAtTrait;

    public const string PREFIX = 'tera-person_';

    #[ORM\Column(nullable: true, options: ['jsonb' => true])]
    #[Assert\Choice(choices: PersonPermission::ALL, multiple: true)]
    #[Groups(['person_api_key:post', 'person_api_key:get'])]
    private array $permissions = [];

    #[ORM\ManyToOne(inversedBy: 'personApiKeys')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Person $person = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['person_api_key:get'])]
    private ?DateTimeInterface $lastUsedDate = null;

    #[ORM\Column(length: 255)]
    #[Groups(['person_api_key:post', 'person_api_key:get'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $jti = null;

    private ?string $token = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['person_api_key:post:output', 'person_api_key:get'])]
    private ?DateTimeImmutable $expirationDate = null;

    #[Groups(['person_api_key:post:output', 'person_api_key:get'])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
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

    public function getRoles(): array
    {
        return [];
    }

    public function eraseCredentials(): void
    {
    }

    public function getJWTPayload(): array
    {
        return [
            'sub' => 'PersonApiKey',
            'name' => $this->getName(),
            'aud' => 'tera',
            'jti' => $this->getJti(),
            'exp' => $this->getExpirationDate()?->getTimestamp(),
        ];
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

    public function getJti(): string
    {
        return $this->jti;
    }

    public function setJti(string $jti): static
    {
        $this->jti = $jti;

        return $this;
    }

    public function getExpirationDate(): ?DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?DateTimeImmutable $expirationDate): static
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->ulid;
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

    public function getLastUsedDate(): ?DateTimeInterface
    {
        return $this->lastUsedDate;
    }

    public function setLastUsedDate(?DateTimeInterface $lastUsedDate): static
    {
        $this->lastUsedDate = $lastUsedDate;

        return $this;
    }

    #[Groups(['person_api_key:post:output'])]
    public function getToken(): string
    {
        $token = $this->token;
        $this->setToken(null);

        return self::PREFIX . $token;
    }

    public function setToken(?string $token): static
    {
        $this->token = $token;

        return $this;
    }
}
