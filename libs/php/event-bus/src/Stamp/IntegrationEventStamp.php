<?php

namespace Lychen\EventBus\Stamp;

use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;

/**
 * Marks a message that came from another service as an integration event, and carries its
 * identity: what InboxMiddleware deduplicates on.
 *
 * On the wire these are plain AMQP headers, readable by any consumer whatever its language,
 * rather than one of Messenger's `X-Message-Stamp-<PHP class>` headers. Hence
 * NonSendableStampInterface: EventSerializer writes the headers itself, so that a retry, or a
 * copy kept by the failure transport, keeps the event's identity.
 */
final readonly class IntegrationEventStamp implements NonSendableStampInterface
{
    public const string TYPE_HEADER = 'type';
    public const string ID_HEADER = 'event_id';
    public const string PRODUCER_HEADER = 'event_producer';
    public const string OCCURRED_AT_HEADER = 'event_occurred_at';

    public function __construct(
        public string $eventId,
        public string $type,
        public ?string $producer = null,
        public ?\DateTimeImmutable $occurredAt = null,
    ) {
    }

    /**
     * The stamp described by a message's headers, or null for a message that is not an
     * integration event (no `event_id` header): Messenger's own background messages.
     *
     * @param array<array-key, mixed> $headers
     */
    public static function fromHeaders(array $headers): ?self
    {
        $eventId = $headers[self::ID_HEADER] ?? null;
        $type = $headers[self::TYPE_HEADER] ?? null;
        if (!\is_string($eventId) || !\is_string($type)) {
            return null;
        }

        $producer = $headers[self::PRODUCER_HEADER] ?? null;
        $occurredAt = $headers[self::OCCURRED_AT_HEADER] ?? null;

        return new self(
            $eventId,
            $type,
            \is_string($producer) ? $producer : null,
            \is_string($occurredAt) ? (\DateTimeImmutable::createFromFormat(\DATE_RFC3339, $occurredAt) ?: null) : null,
        );
    }

    /**
     * The headers describing the event, but its type: the serializer writes that one.
     *
     * @return array<string, string>
     */
    public function toHeaders(): array
    {
        return array_filter([
            self::ID_HEADER => $this->eventId,
            self::PRODUCER_HEADER => $this->producer,
            self::OCCURRED_AT_HEADER => $this->occurredAt?->format(\DATE_RFC3339),
        ], static fn (?string $value): bool => null !== $value);
    }
}
