<?php

namespace App\Security\JWT;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

readonly class JWTDecoder
{
    public function __construct(private string $secret,
        private string $algorithm)
    {
    }

    public function decode(string $jwt): \stdClass
    {
        return JWT::decode($jwt, new Key($this->secret, $this->algorithm));
    }
}
