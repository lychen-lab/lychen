<?php

namespace Lychen\UtilZitadelBundle\UserProvider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Lychen\UtilZitadelBundle\Interface\AuthIdIdentifiedInterface;
use Lychen\UtilZitadelBundle\Interface\HasEmailInterface;
use Lychen\UtilZitadelBundle\Interface\HasRolesInterface;
use Lychen\UtilZitadelBundle\Interface\NamedEntityInterface;
use Lychen\UtilZitadelBundle\Services\OpenIDConnect;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

readonly class ZitadelUserProvider implements UserProviderInterface
{
    public const ROLES_CLAIM = 'urn:zitadel:iam:org:project:roles';
    public const SCOPES = ['openid', 'profile', 'email', self::ROLES_CLAIM];

    private EntityRepository $repository;

    public function __construct(private LoggerInterface $logger, private OpenIDConnect $openIDConnect, private EntityManagerInterface $entityManager, private string $userClass)
    {
        $this->repository = $this->entityManager->getRepository($this->userClass);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof AuthIdIdentifiedInterface) {
            throw new UnsupportedUserException('Invalid user class '.get_class($user));
        }

        $refreshedUser = $this->repository->find($user->getId());
        if (!$refreshedUser) {
            throw new UserNotFoundException('User with id '.$user->getId().' not found.');
        }

        return $refreshedUser;
    }

    public function supportsClass(string $class): bool
    {
        return $this->userClass === $class || is_subclass_of($class, $this->userClass);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $response = $this->openIDConnect->userinfo($identifier);

        $this->ensureUserExists($response['sub'], $response);

        $user = $this->repository->findOneBy(['authId' => $response['sub']]);

        if (null === $user) {
            throw new UserNotFoundException('User with id '.$identifier.' not found.');
        }

        return $user;
    }

    public function ensureUserExists(string $userIdentifier, array $userData): void
    {
        $this->logger->debug('OIDC User Data', [
            'sub' => $userData['sub'],
            'given_name' => $userData['given_name'],
            'family_name' => $userData['family_name'],
        ]);
        try {
            $user = $this->repository->findOneBy(['authId' => $userIdentifier]);
            if (!$user) {
                $user = new $this->userClass();
                $this->entityManager->persist($user);
            }
            $this->updateUserEntity($user, $userData);
            $this->entityManager->flush();
        } catch (\Throwable $th) {
            throw new \Exception('Cannot create user', previous: $th);
        }
    }

    private function updateUserEntity(AuthIdIdentifiedInterface&NamedEntityInterface&HasRolesInterface&HasEmailInterface &$user, array $userData): void
    {
        $user->setAuthId($userData['sub']);
        $user->setEmail($userData['email']);
        $user->setGivenName($userData['given_name']);
        $user->setFamilyName($userData['family_name']);
        $user->setRoles($this->parseZitadelRoles($userData));
    }

    private function parseZitadelRoles(array $userData): array
    {
        $roles = [];
        if (isset($userData[self::ROLES_CLAIM]) && is_array($userData[self::ROLES_CLAIM])) {
            foreach ($userData[self::ROLES_CLAIM] as $roleKey => $orgData) {
                $roles[] = $roleKey;
            }
        }

        return $roles;
    }
}
