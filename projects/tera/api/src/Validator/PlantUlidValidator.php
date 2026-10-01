<?php

namespace App\Validator;

use App\Service\PlantVerifier;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PlantUlidValidator extends ConstraintValidator
{
    public function __construct(private readonly PlantVerifier $plantVerifier)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var PlantUlid $constraint */
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
