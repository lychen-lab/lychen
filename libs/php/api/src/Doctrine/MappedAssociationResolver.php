<?php

namespace Lychen\Api\Doctrine;

use Doctrine\Persistence\ManagerRegistry;
use Lychen\UtilModel\Interface\UuidIdentifiedInterface;
use Symfony\Component\ObjectMapper\Attribute\Map;

/**
 * Tells which entity a property of an input DTO refers to, from the object mapper and
 * Doctrine mappings, so that no relation has to name it again:
 *
 * - the entity the DTO is mapped onto: its class-level #[Map(target: Entity::class)];
 * - the association the property is mapped onto: the property's name, or its
 *   #[Map(target: 'otherName')] when the DTO renames it;
 * - the class that Doctrine association targets.
 */
final readonly class MappedAssociationResolver
{
    public function __construct(private ManagerRegistry $registry)
    {
    }

    /**
     * @param class-string      $dtoClass
     * @param class-string|null $entityClass the entity the DTO is mapped onto, read from the DTO when null
     *
     * @return class-string<UuidIdentifiedInterface>
     */
    public function targetEntityClass(string $dtoClass, string $property, ?string $entityClass = null): string
    {
        $entityClass ??= $this->mappedEntityClass($dtoClass);
        $association = $this->mappedPropertyName($dtoClass, $property);

        $metadata = $this->registry->getManagerForClass($entityClass)?->getClassMetadata($entityClass);
        if (null === $metadata || !$metadata->hasAssociation($association)) {
            throw new \LogicException(\sprintf('"%s::$%s" is mapped onto "%s::$%s", which is not a Doctrine association: name the entity its uuids identify.', $dtoClass, $property, $entityClass, $association));
        }

        $targetClass = $metadata->getAssociationTargetClass($association);
        if (null === $targetClass || !is_a($targetClass, UuidIdentifiedInterface::class, true)) {
            throw new \LogicException(\sprintf('"%s::$%s" does not target an entity identified by a uuid (%s).', $entityClass, $association, UuidIdentifiedInterface::class));
        }

        return $targetClass;
    }

    /**
     * @param class-string $dtoClass
     *
     * @return class-string
     */
    private function mappedEntityClass(string $dtoClass): string
    {
        $entityClasses = [];
        foreach ((new \ReflectionClass($dtoClass))->getAttributes(Map::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $target = $attribute->newInstance()->target;
            if (null !== $target && class_exists($target)) {
                $entityClasses[] = $target;
            }
        }

        if (1 !== \count($entityClasses)) {
            throw new \LogicException(\sprintf('"%s" must be mapped onto exactly one entity, with #[Map(target: Entity::class)].', $dtoClass));
        }

        return $entityClasses[0];
    }

    /**
     * @param class-string $dtoClass
     */
    private function mappedPropertyName(string $dtoClass, string $property): string
    {
        foreach ((new \ReflectionProperty($dtoClass, $property))->getAttributes(Map::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            $target = $attribute->newInstance()->target;
            if (null !== $target && !class_exists($target)) {
                return $target;
            }
        }

        return $property;
    }
}
