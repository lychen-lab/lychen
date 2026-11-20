<?php

namespace App\Validator;

use App\Entity\Interface\StateLandInterface;
use App\Repository\LandProposalRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class LandProposalOnlyOneStatePerLandValidator extends ConstraintValidator
{
    public function __construct(private readonly LandProposalRepository $repository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof LandProposalOnlyOneStatePerLand) {
            throw new UnexpectedTypeException($constraint, LandProposalOnlyOneStatePerLand::class);
        }

        if (!$value instanceof StateLandInterface) {
            throw new UnexpectedTypeException($value, StateLandInterface::class);
        }

        if (!$value->getLand() && !$value->getState()) {
            return;
        }

        if (!in_array($value->getState(), $constraint->states, true)) {
            return;
        }

        foreach ($constraint->states as $state) {
            if ($this->repository->hasExistingStateForLand($value->getLand(), $state, $value->getId())) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ state }}', $state)
                    ->atPath('state')
                    ->addViolation();
            }
        }
    }
}
