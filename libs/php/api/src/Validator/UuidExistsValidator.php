<?php

namespace Lychen\Api\Validator;

use Lychen\Api\Doctrine\MappedAssociationResolver;
use Lychen\Api\Doctrine\UuidEntityFinder;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UuidExistsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly MappedAssociationResolver $associations,
        private readonly UuidEntityFinder $finder,
    ) {
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

        $entityClass = $constraint->entity ?? $this->mappedEntityClass();
        $isList = \is_array($value);
        $uuids = $isList ? array_values($value) : [$value];

        $found = $this->finder->findByUuids($entityClass, $uuids);
        $entity = (new \ReflectionClass($entityClass))->getShortName();

        foreach ($uuids as $index => $uuid) {
            $key = UuidEntityFinder::key($uuid);
            if (null !== $key && isset($found[$key])) {
                continue;
            }

            $violation = $this->context->buildViolation($constraint->message)
                ->setParameter('{{ uuid }}', $key ?? (\is_string($uuid) ? $uuid : get_debug_type($uuid)))
                ->setParameter('{{ entity }}', $entity)
                ->setInvalidValue($uuid)
                ->setCode(UuidExists::NOT_FOUND_ERROR);

            if ($isList) {
                $violation->atPath(\sprintf('[%d]', $index));
            }

            $violation->addViolation();
        }
    }

    /**
     * Unlike an object mapper transform, a validator knows the property it validates: the
     * entity follows from the DTO's mapping.
     *
     * @return class-string
     */
    private function mappedEntityClass(): string
    {
        $dto = $this->context->getObject();
        $property = $this->context->getPropertyName();

        if (null === $dto || null === $property) {
            throw new \LogicException('#[UuidExists] without an entity must be put on a property of a DTO mapped onto an entity (#[Map(target: Entity::class)]).');
        }

        return $this->associations->targetEntityClass($dto::class, $property);
    }
}
