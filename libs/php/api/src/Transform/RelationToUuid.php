<?php

namespace Lychen\Api\Transform;

use Lychen\Api\UuidContract;
use Symfony\Component\ObjectMapper\TransformCallableInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Object mapper transform exposing a relation by the uuid of its entities, instead of
 * nesting the related resources or linking them by IRI.
 *
 * Entity -> resource direction, on the properties of an API resource class:
 *
 *     #[Map(source: 'seedLibrary', transform: RelationToUuid::class)]
 *     public ?Uuid $seedLibrary = null;
 *
 *     #[Map(source: 'characteristics', transform: RelationToUuid::class)]
 *     public array $characteristics = [];
 *
 * As a service (LychenApiBundle tags it object_mapper.transform_callable), it tells a
 * ManyToOne / OneToOne (an entity: its uuid) from a OneToMany / ManyToMany (a collection:
 * the list of uuids) by the value it gets. The static one() and many() do the same as
 * callables, [RelationToUuid::class, 'one'] and [RelationToUuid::class, 'many'], but the
 * object mapper only calls such an array when is_callable() accepts it: with a wrong class
 * or method name, it reads the array as a list of transform services instead, and fails
 * with '"...RelationToUuid" is not a valid callable'.
 *
 * @implements TransformCallableInterface<object, object>
 */
final class RelationToUuid implements TransformCallableInterface
{
    /**
     * @return Uuid|list<Uuid>|null
     */
    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        if (null === $value) {
            return null;
        }

        if (is_iterable($value)) {
            return self::many($value);
        }

        if (\is_object($value)) {
            return self::one($value);
        }

        throw new \InvalidArgumentException(\sprintf('Expected an entity or a collection of entities, got "%s".', get_debug_type($value)));
    }

    /**
     * A ManyToOne / OneToOne: its uuid, or null.
     */
    public static function one(?object $entity): ?Uuid
    {
        return null === $entity ? null : UuidContract::uuidOf($entity);
    }

    /**
     * A OneToMany / ManyToMany: the uuid of each entity of the collection.
     *
     * @param iterable<mixed> $entities
     *
     * @return list<Uuid>
     */
    public static function many(iterable $entities): array
    {
        $uuids = [];
        foreach ($entities as $entity) {
            if (!\is_object($entity)) {
                throw new \InvalidArgumentException(\sprintf('Expected an entity, got "%s".', get_debug_type($entity)));
            }
            $uuids[] = UuidContract::uuidOf($entity);
        }

        return $uuids;
    }
}
