<?php

namespace App\Validator;

use App\Service\PlantVerifier;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PlantUlidValidator extends ConstraintValidator
{
    public function __construct(private readonly PlantVerifier $plantVerifier)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PlantUlid) {
            throw new UnexpectedTypeException($constraint, PlantUlid::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        try {
            $this->plantVerifier->assertPlantExists($value);
        } catch (\Exception $e) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
