<?php

namespace App\Validator;

use App\Entity\LandMember;
use App\Entity\LandMemberInvitation;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class LandRolesBelongToLandValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof LandRolesBelongToLand) {
            throw new UnexpectedTypeException($constraint, LandRolesBelongToLand::class);
        }

        if (!$value instanceof LandMember && !$value instanceof LandMemberInvitation) {
            return;
        }

        $land = $value->getLand();
        $landRoles = $value->getLandRoles();

        if (null === $land || $landRoles->isEmpty()) {
            return;
        }

        foreach ($landRoles as $landRole) {
            if ($landRole->getLand() !== $land) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('landRoles')
                    ->addViolation();

                return;
            }
        }
    }
}
