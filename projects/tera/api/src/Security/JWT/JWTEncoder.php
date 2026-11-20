<?php

namespace App\Security\JWT;

use Firebase\JWT\JWT;
use Symfony\Component\Uid\Uuid;

readonly class JWTEncoder
{
    public const string ISSUER = 'tera';

    public function __construct(private string $secret,
        private string $algorithm)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function preparePayload(JWTPayloadable $subject): array
    {
        $subject->setJti(Uuid::v4());

        return array_merge($subject->getJWTPayload(), [
            'iat' => time(),
            'iss' => self::ISSUER,
        ]);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function encode(array $payload): string
    {
        return JWT::encode($payload, $this->secret, $this->algorithm);
    }
}
