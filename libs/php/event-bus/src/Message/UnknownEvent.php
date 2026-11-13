<?php

namespace Lychen\EventBus\Message;

/**
 * An integration event that reached this service's queue but that none of its classes
 * declares (#[AsMessage(serializedTypeName: ...)]): EventSerializer decodes it into this, and
 * UnknownEventHandler acknowledges it with a warning.
 *
 * It happens when a binding is broader than the events the service handles (`flora.seed_lot.#`
 * for a service that only reads `flora.seed_lot.given.v1`), or when a producer starts
 * publishing a version the service does not read yet.
 */
final readonly class UnknownEvent
{
    public function __construct(
        public string $type,
        public string $eventId,
        public ?string $producer = null,
    ) {
    }
}
