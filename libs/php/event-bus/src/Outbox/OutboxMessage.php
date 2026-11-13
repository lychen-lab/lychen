<?php

namespace Lychen\EventBus\Outbox;

/**
 * An outbox row on its way to the broker: what OutboxRelay hands to the Messenger sender.
 *
 * It carries the payload already serialized, which EventSerializer sends byte for byte, so
 * the relay never needs the event's class.
 */
final readonly class OutboxMessage
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        public string $id,
        public string $type,
        public string $payload,
        public array $headers,
        public \DateTimeImmutable $occurredAt,
        public string $producer,
    ) {
    }
}
