<?php

namespace App\Api\Transform;

use App\Entity\Person;

/**
 * Object mapper transforms exposing a person's name without exposing the Person itself.
 */
final class PersonName
{
    public static function givenName(?Person $person): ?string
    {
        return $person?->getGivenName();
    }

    public static function familyName(?Person $person): ?string
    {
        return $person?->getFamilyName();
    }
}
