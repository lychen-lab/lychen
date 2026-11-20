<?php

namespace App\Security\Service;

use App\Entity\Person;
use App\Entity\PersonApiKey;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * The person the authenticated user acts for, to scope what is relative to a person: a Person
 * itself, or the person a PersonApiKey stands for. Never the user's own id, which an API key
 * shares with a person of another table.
 *
 * Null for a LandApiKey, which stands for a land and no person, and when no one is authenticated:
 * a list relative to a person then has nothing to show.
 */
final readonly class ActingPersonResolver
{
    public function __construct(private Security $security)
    {
    }

    public function resolve(): ?Person
    {
        $user = $this->security->getUser();

        return match (true) {
            $user instanceof Person => $user,
            $user instanceof PersonApiKey => $user->getPerson(),
            default => null,
        };
    }
}
