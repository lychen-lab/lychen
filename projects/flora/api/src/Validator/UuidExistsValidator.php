<?php

namespace App\Validator;

use App\Doctrine\UuidEntityFinder;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class UuidExistsValidator extends ConstraintValidator
{
    public function __construct(private readonly UuidEntityFinder $finder)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UuidExists) {
            throw new UnexpectedTypeException($constraint, UuidExists::class);
        }

        // Absent from a PATCH, or not sent: nothing to check (NotNull says if it must be).
        if (null === $value || [] === $value) {
            return;
        }

        $isList = \is_array($value);
        $values = $isList ? $value : [$value];
        $uuids = [];
        foreach ($values as $uuid) {
            if (!$uuid instanceof Uuid) {
                throw new UnexpectedValueException($uuid, Uuid::class);
            }
            $uuids[] = $uuid;
        }

        $found = $this->finder->findByUuids($constraint->entity, $uuids);
        $entity = (new \ReflectionClass($constraint->entity))->getShortName();

        foreach ($uuids as $index => $uuid) {
            if (isset($found[$uuid->toRfc4122()])) {
                continue;
            }

            $violation = $this->context->buildViolation($constraint->message)
                ->setParameter('{{ uuid }}', $uuid->toRfc4122())
                ->setParameter('{{ entity }}', $entity)
                ->setInvalidValue($uuid)
                ->setCode(UuidExists::NOT_FOUND_ERROR);

            if ($isList) {
                $violation->atPath(\sprintf('[%d]', $index));
            }

            $violation->addViolation();
        }
    }
}
