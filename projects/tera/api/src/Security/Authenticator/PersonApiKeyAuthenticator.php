<?php

namespace App\Security\Authenticator;

use App\Entity\PersonApiKey;
use App\Security\JWT\JWTDecoder;
use App\Security\JWT\JWTValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class PersonApiKeyAuthenticator extends AbstractAuthenticator
{
    public const string HEADER_ATTRIBUTE = 'tera-personal-token';

    public function __construct(private readonly EntityManagerInterface $entityManager,
        private readonly JWTDecoder $JWTDecoder,
        private readonly JWTValidator $JWTValidator)
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has(self::HEADER_ATTRIBUTE);
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->headers->get(self::HEADER_ATTRIBUTE);

        if (null === $token) {
            throw new CustomUserMessageAuthenticationException(sprintf('Authentication failed: Missing required header "%s"', self::HEADER_ATTRIBUTE));
        }

        if (!str_starts_with($token, PersonApiKey::PREFIX)) {
            throw new CustomUserMessageAuthenticationException('Invalid token format : missing prefix.');
        }

        try {
            $token = substr($token, strlen(PersonApiKey::PREFIX));
            $decodedToken = $this->JWTDecoder->decode($token);
            if (!$this->JWTValidator->isValid($decodedToken)) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            throw new CustomUserMessageAuthenticationException('Invalid token');
        }

        return new SelfValidatingPassport(new UserBadge($decodedToken->jti));
    }

    public function onAuthenticationSuccess(Request $request,
        TokenInterface $token,
        string $firewallName): ?Response
    {
        /** @var PersonApiKey $personApiKey */
        $personApiKey = $token->getUser();
        $personApiKey->setLastUsedDate(new \DateTime());
        $this->entityManager->persist($personApiKey);
        $this->entityManager->flush();

        return null;
    }

    public function onAuthenticationFailure(Request $request,
        AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
