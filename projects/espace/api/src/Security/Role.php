<?php

namespace App\Security;

final class Role
{
    /**
     * Moderators publish or reject the listings waiting for validation.
     *
     * This is the key of the role in Zitadel: ZitadelUserProvider copies the role keys of
     * the token as they are onto Person::$roles, and Symfony only votes on roles whose
     * name starts with ROLE_.
     */
    public const string MODERATOR = 'ROLE_ESPACE_MODERATOR';
}
