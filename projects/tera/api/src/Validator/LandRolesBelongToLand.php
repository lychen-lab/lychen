<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class LandRolesBelongToLand extends Constraint
{
    public string $message = 'The land roles must belong to the same land as the land member.';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}
