<?php

namespace App\Security\JWT;

use Firebase\JWT\JWT;
use Symfony\Component\Uid\Uuid;

readonly class JWTEncoder
{
    public const string ISSUER = 'flora';

    public function __construct(private string $secret,
        private string $algorithm)
    {
    }

    public function preparePayload(JWTPayloadable $subject): array
    {
        $subject->setJti(Uuid::v4());

        return array_merge($subject->getJWTPayload(), [
            'iat' => time(),
            'iss' => self::ISSUER,
        ]);
    }

    public function encode(array $payload): string
    {
        return JWT::encode($payload, $this->secret, $this->algorithm);
    }
}
