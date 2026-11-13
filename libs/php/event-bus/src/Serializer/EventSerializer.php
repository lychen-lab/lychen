<?php

namespace Lychen\EventBus\Serializer;

use Lychen\EventBus\Message\UnknownEvent;
use Lychen\EventBus\Outbox\OutboxMessage;
use Lychen\EventBus\Stamp\IntegrationEventStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\MessageDecodingFailedException;
use Symfony\Component\Messenger\Transport\Serialization\MessageTypeAwareSerializerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Uid\Uuid;

/**
 * The transport serializer of a service's queue: Messenger's Symfony serializer (JSON, with
 * the `type` header naming the message), plus what integration events need.
 *
 * The `type` header of an event is its serialized type name, `flora.seed_lot.given.v1`, which
 * Messenger maps back to the class declaring it with #[AsMessage(serializedTypeName: ...)].
 * Each service declares its own class for the events it publishes and for those it consumes:
 * services share the wire format, never PHP code.
 *
 * On top of the inner serializer:
 *
 * - encode() sends an OutboxMessage as it was recorded, payload byte for byte, with the
 *   event's headers (`event_id`, `event_producer`, `event_occurred_at`).
 * - decode() marks an incoming event with an IntegrationEventStamp, read from those headers,
 *   which InboxMiddleware deduplicates on; encode() writes them back, so a retry or a copy in
 *   the failure transport is still recognized as the same event.
 * - An event no class of this service declares decodes into an UnknownEvent, which the bundle
 *   acknowledges with a warning instead of failing it forever: the queue's bindings decide
 *   what reaches it, and a broader binding, or a new version of an event, should not poison it.
 *
 * Messages without an `event_id` header (the service's own background work) go through the
 * inner serializer untouched.
 */
final readonly class EventSerializer implements SerializerInterface, MessageTypeAwareSerializerInterface
{
    public function __construct(
        private SerializerInterface&MessageTypeAwareSerializerInterface $inner,
    ) {
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        $headers = \is_array($encodedEnvelope['headers'] ?? null) ? $encodedEnvelope['headers'] : [];
        $stamp = IntegrationEventStamp::fromHeaders($headers);

        if (null === $stamp) {
            return $this->inner->decode($encodedEnvelope);
        }

        if (!Uuid::isValid($stamp->eventId)) {
            return MessageDecodingFailedException::wrap($encodedEnvelope, \sprintf('The "%s" header of event "%s" is not a UUID: "%s".', IntegrationEventStamp::ID_HEADER, $stamp->type, $stamp->eventId));
        }

        $class = $this->getMessageType($encodedEnvelope);
        if (null === $class || !class_exists($class)) {
            return new Envelope(new UnknownEvent($stamp->type, $stamp->eventId, $stamp->producer));
        }

        return $this->inner->decode($encodedEnvelope)->with($stamp);
    }

    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();

        if ($message instanceof OutboxMessage) {
            $event = new IntegrationEventStamp($message->id, $message->type, $message->producer, $message->occurredAt);

            return [
                'body' => $message->payload,
                'headers' => [
                    ...$message->headers,
                    IntegrationEventStamp::TYPE_HEADER => $message->type,
                    ...$event->toHeaders(),
                ],
            ];
        }

        $encoded = $this->inner->encode($envelope);

        $event = $envelope->last(IntegrationEventStamp::class);
        if ($event instanceof IntegrationEventStamp) {
            $encoded['headers'] = [...$encoded['headers'] ?? [], ...$event->toHeaders()];
        }

        return $encoded;
    }

    public function getMessageType(array $encodedEnvelope): ?string
    {
        return $this->inner->getMessageType($encodedEnvelope);
    }
}
