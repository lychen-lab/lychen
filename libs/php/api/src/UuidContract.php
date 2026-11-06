<?php

namespace Lychen\Api;

use Lychen\Api\Interface\UuidIdentifiedInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Checks the uuid contract of UuidIdentifiedInterface by its shape rather than by its name.
 *
 * The contract is identical in lychen/model, whose entities implement its own
 * UuidIdentifiedInterface: the two packages stay independent, so neither interface can
 * extend the other. An entity fulfills the contract when it implements this library's
 * interface, or has a getUuid() returning a Uuid, whichever interface declares it.
 */
final class UuidContract
{
    /**
     * @param class-string $class
     */
    public static function isFulfilledBy(string $class): bool
    {
        if (is_a($class, UuidIdentifiedInterface::class, true)) {
            return true;
        }

        if (!method_exists($class, 'getUuid')) {
            return false;
        }

        $returnType = (new \ReflectionMethod($class, 'getUuid'))->getReturnType();

        return $returnType instanceof \ReflectionNamedType && is_a($returnType->getName(), Uuid::class, true);
    }

    public static function uuidOf(object $entity): Uuid
    {
        $uuid = method_exists($entity, 'getUuid') ? $entity->getUuid() : null;

        if (!$uuid instanceof Uuid) {
            throw new \LogicException(\sprintf('"%s" does not fulfill the contract of %s: it needs a getUuid() returning a Uuid.', $entity::class, UuidIdentifiedInterface::class));
        }

        return $uuid;
    }
}
