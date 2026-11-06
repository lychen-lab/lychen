<?php

namespace Lychen\Api\Transform;

use Lychen\Api\UuidContract;
use Symfony\Component\Uid\Uuid;

/**
 * Object mapper transforms exposing a relation by the uuid of its entities, instead of
 * nesting the related resources or linking them by IRI.
 *
 * Entity -> resource direction, on the properties of an API resource class:
 *
 *     #[Map(source: 'seedLibrary', transform: [RelationToUuid::class, 'one'])]
 *     public ?Uuid $seedLibrary = null;
 *
 *     #[Map(source: 'characteristics', transform: [RelationToUuid::class, 'many'])]
 *     public array $characteristics = [];
 */
final class RelationToUuid
{
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
     * @param iterable<object> $entities
     *
     * @return list<Uuid>
     */
    public static function many(iterable $entities): array
    {
        $uuids = [];
        foreach ($entities as $entity) {
            $uuids[] = UuidContract::uuidOf($entity);
        }

        return $uuids;
    }
}
