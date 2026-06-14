<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Filter\LandFilter;
use App\Repository\LandApiKeyRepository;
use App\Security\Constant\LandMemberPermission;
use App\Security\Interface\LandAwareInterface;
use App\Security\Interface\PermissionHolder;
use App\Security\JWT\JWTPayloadable;
use App\Security\Voter\LandApiKeyVoter;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Lychen\UtilModel\Abstract\AbstractIdOrmAndUlidApiIdentified;
use Lychen\UtilModel\Trait\CreatedAtTrait;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LandApiKeyRepository::class)]
#[ApiResource]
#[Post(
    normalizationContext   : ['groups' => ['land_api_key:post', 'land_api_key:post:output']],
    denormalizationContext : ['groups' => ['land_api_key:post', 'land_api_key:post:input']],
    securityPostDenormalize: "is_granted('" . LandApiKeyVoter::POST . "', object)"
)]
#[Delete(
    security: "is_granted('" . LandApiKeyVoter::DELETE . "', object)"
)]
#[Get(
    normalizationContext: ['groups' => ['land_api_key:get']],
    security            : "is_granted('" . LandApiKeyVoter::GET . "', object)"
)]
#[GetCollection(
    normalizationContext: ['groups' => ['land_api_key:collection']],
    security            : "is_granted('" . LandApiKeyVoter::COLLECTION . "')",
    parameters          : [
        new QueryParameter(
            key   : 'land',
            filter: LandFilter::class,
        ),
    ]
)]
#[ORM\HasLifecycleCallbacks]
class LandApiKey extends AbstractIdOrmAndUlidApiIdentified implements PermissionHolder, UserInterface, JWTPayloadable, LandAwareInterface
{
    use CreatedAtTrait;

    public const string PREFIX = 'tera-land_';

    #[ORM\Column(nullable: true, options: ['jsonb' => true])]
    #[Assert\Choice(choices: LandMemberPermission::ALL, multiple: true)]
    #[Groups(['land_api_key:post', 'land_api_key:get'])]
    private array $permissions = [];

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['land_api_key:get'])]
    private ?DateTimeInterface $lastUsedDate = null;

    #[ORM\Column(length: 255)]
    #[Groups(['land_api_key:post', 'land_api_key:get'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'landApiKeys')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['land_api_key:post', 'land_api_key:get'])]
    private ?Land $land = null;

    #[ORM\Column(length: 255)]
    private ?string $jti = null;

    private ?string $token = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['land_api_key:post:output', 'land_api_key:get'])]
    private ?DateTimeImmutable $expirationDate = null;

    #[Groups(['land_api_key:post:output', 'land_api_key:get'])]
    public function getUlid(): Ulid
    {
        return parent::getUlid();
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

    public function getApiKeys(): array
    {
        return [];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {

        return $this->ulid;
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

    public function getLand(): ?Land
    {
        return $this->land;
    }

    public function setLand(?Land $land): static
    {
        $this->land = $land;

        return $this;
    }

    public function getRoles(): array
    {
        return [];
    }

    public function getJWTPayload(): array
    {
        return [
            'sub' => 'LandApiKey',
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

    #[Groups(['land_api_key:post:output'])]
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
