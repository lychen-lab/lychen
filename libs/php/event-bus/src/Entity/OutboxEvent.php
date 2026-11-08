<?php

namespace Lychen\EventBus\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * An event recorded by the transaction that made it happen, waiting for the relay to publish
 * it (see Outbox and OutboxRelay).
 *
 * The payload is kept as the exact bytes the relay will publish (TEXT, not JSONB, which would
 * reorder the keys), already serialized when the event is recorded: the relay never needs the
 * event's class.
 *
 * Column names are pinned, since the relay and the purge reach this table with plain SQL,
 * whatever naming strategy the API configures.
 */
#[ORM\Entity]
#[ORM\Table(name: 'outbox_event')]
#[ORM\Index(name: 'outbox_event_published_at_idx', columns: ['published_at'])]
class OutboxEvent
{
    #[ORM\Column(name: 'published_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    /**
     * @param array<string, string> $headers the transport headers that go with the payload,
     *                                       such as its Content-Type
     */
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(name: 'id', type: UuidType::NAME)]
        private Uuid $id,
        #[ORM\Column(name: 'type', length: 255)]
        private string $type,
        #[ORM\Column(name: 'payload', type: Types::TEXT)]
        private string $payload,
        #[ORM\Column(name: 'headers', type: Types::JSON)]
        private array $headers,
        #[ORM\Column(name: 'occurred_at', type: Types::DATETIME_IMMUTABLE)]
        private \DateTimeImmutable $occurredAt,
    ) {
    }

    /**
     * The event's identity, unique across services: the AMQP message_id, and the key a
     * consumer deduplicates on.
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * The serialized type name, `<domain>.<aggregate>.<action>.v<n>`: also the routing key the
     * event is published under.
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getOccurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }

    /**
     * When the broker confirmed it, null while it waits for the relay.
     */
    public function getPublishedAt(): ?\DateTimeImmutable
    {
        return $this->publishedAt;
    }
}
