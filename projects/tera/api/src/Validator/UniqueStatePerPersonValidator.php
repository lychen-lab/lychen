<?php

namespace App\Validator;

use App\Entity\Interface\StatePersonInterface;
use App\Entity\Person;
use App\Repository\LandRequestRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueStatePerPersonValidator extends ConstraintValidator
{
    public function __construct(private readonly LandRequestRepository $repository, private readonly Security $security)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueStatePerPerson) {
            throw new UnexpectedTypeException($constraint, UniqueStatePerPerson::class);
        }

        if (!$value instanceof StatePersonInterface) {
            throw new UnexpectedTypeException($value, StatePersonInterface::class);
        }

        if (!$value->getPerson()) {
            $authenticatedUser = $this->security->getUser();
            if (!$authenticatedUser instanceof Person) {
                return;
            }
            $value->setPerson($authenticatedUser);
        }

        if (!$value->getState()) {
            return;
        }

        if (!in_array($value->getState(), $constraint->states, true)) {
            return;
        }

        foreach ($constraint->states as $state) {
            if ($this->repository->hasExistingStateForPerson($value->getPerson(), $state, $value->getId())) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ state }}', $state)
                    ->atPath('state')
                    ->addViolation();
            }
        }
    }
}
