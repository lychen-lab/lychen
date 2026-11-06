<?php

namespace Lychen\Api\Transform;

use ApiPlatform\Validator\Exception\ValidationException;
use Lychen\Api\Doctrine\MappedAssociationResolver;
use Lychen\Api\Doctrine\UuidEntityFinder;
use Lychen\Api\Validator\UuidExists;
use Symfony\Component\ObjectMapper\TransformCallableInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Object mapper transform turning the uuid (or list of uuids) of an input DTO property into
 * the entity (or entities) to link, for any relation.
 *
 * Input DTO -> entity direction, on the properties of a POST or PATCH DTO:
 *
 *     #[UuidExists]
 *     #[Map(transform: UuidToEntity::class)]
 *     public Uuid $seedLibrary;
 *
 * - a uuid becomes the entity: the mapper hands it to the setter (setSeedLibrary());
 * - a list of uuids becomes the list of entities: the mapper syncs the collection with
 *   it through the adder and remover (addCharacteristic() / removeCharacteristic()), so
 *   `[]` unlinks them all.
 *
 * The entity class comes from the Doctrine association the property is mapped onto
 * (MappedAssociationResolver). A transform gets the value but not the property name
 * (symfony/object-mapper 8.1), so the property is found among the DTO's public properties
 * by its value: an empty list returns early, since every empty list looks alike, and two
 * properties holding the same value are refused rather than guessed.
 *
 * #[UuidExists] reports unknown uuids, all at once, when API Platform validates the DTO,
 * before any mapping. The ValidationException thrown here for an unknown uuid is a safety
 * net for a property lacking that constraint.
 *
 * @implements TransformCallableInterface<object, object>
 */
final readonly class UuidToEntity implements TransformCallableInterface
{
    public function __construct(
        private MappedAssociationResolver $associations,
        private UuidEntityFinder $finder,
    ) {
    }

    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        if (null === $value) {
            return null;
        }

        if ([] === $value) {
            return [];
        }

        if (null === $target) {
            throw new \LogicException(\sprintf('%s maps a property: put it on a property, not on a class.', self::class));
        }

        $property = $this->findSourceProperty($source, $value);
        $entityClass = $this->associations->targetEntityClass($source::class, $property, $target::class);

        $isList = \is_array($value);
        $uuids = $isList ? array_values($value) : [$value];
        $found = $this->finder->findByUuids($entityClass, $uuids);

        $entities = [];
        foreach ($uuids as $index => $uuid) {
            $entity = $found[UuidEntityFinder::key($uuid) ?? ''] ?? null;
            if (null === $entity) {
                throw $this->notFound($source, $isList ? \sprintf('%s[%d]', $property, $index) : $property, $uuid, $entityClass);
            }
            $entities[] = $entity;
        }

        return $isList ? $entities : $entities[0];
    }

    private function findSourceProperty(object $source, mixed $value): string
    {
        $matches = array_keys(array_filter(
            get_object_vars($source),
            static fn (mixed $propertyValue): bool => $propertyValue === $value,
        ));

        if ([] === $matches) {
            throw new \LogicException(\sprintf('Cannot find the property of "%s" being mapped: %s needs the raw value of a public property.', $source::class, self::class));
        }

        if (1 < \count($matches)) {
            throw new \LogicException(\sprintf('Cannot tell which property of "%s" is being mapped: "%s" hold the same value.', $source::class, implode('", "', $matches)));
        }

        return $matches[0];
    }

    /**
     * @param class-string $entityClass
     */
    private function notFound(object $source, string $propertyPath, mixed $uuid, string $entityClass): ValidationException
    {
        $parameters = [
            '{{ uuid }}' => UuidEntityFinder::key($uuid) ?? get_debug_type($uuid),
            '{{ entity }}' => (new \ReflectionClass($entityClass))->getShortName(),
        ];

        return new ValidationException(new ConstraintViolationList([
            new ConstraintViolation(strtr(UuidExists::MESSAGE, $parameters), UuidExists::MESSAGE, $parameters, $source, $propertyPath, $uuid, null, UuidExists::NOT_FOUND_ERROR),
        ]));
    }
}
