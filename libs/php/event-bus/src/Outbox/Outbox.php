<?php

namespace Lychen\EventBus\Outbox;

use Doctrine\Persistence\ManagerRegistry;
use Lychen\EventBus\Entity\OutboxEvent;
use Psr\Clock\ClockInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Records an integration event in the outbox, to be written by the same flush, and so the
 * same commit, as the business change it reports: either both happen or neither does. The
 * relay publishes it once committed (see OutboxRelay); nothing reaches the broker from here.
 *
 *     #[AsMessage(serializedTypeName: 'flora.seed_lot.given.v1')]
 *     final readonly class SeedLotGiven { ... }
 *
 *     $outbox->record(new SeedLotGiven(...)); // then the usual flush
 *
 * The event's serialized type name is its routing key, `<domain>.<aggregate>.<action>.v<n>`,
 * and must start with this service's domain: the broker only lets a service publish under its
 * own namespace. It is serialized right away by Messenger's Symfony serializer, the one the
 * consumers decode with, and stored as is.
 */
final readonly class Outbox
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private SerializerInterface $serializer,
        private string $producer,
        private ClockInterface $clock,
    ) {
    }

    /**
     * Persists the event; the caller's flush writes it, inside the caller's transaction.
     */
    public function record(object $event): OutboxEvent
    {
        $encoded = $this->serializer->encode(new Envelope($event));
        $headers = $encoded['headers'] ?? [];
        $type = $headers['type'] ?? null;

        if (!\is_string($type) || 1 !== preg_match($this->typePattern(), $type)) {
            throw new \LogicException(\sprintf('"%s" cannot be recorded as an event of "%2$s": its serialized type is "%3$s". Give it #[AsMessage(serializedTypeName: \'%2$s.<aggregate>.<action>.v<n>\')], the routing key it is published under.', $event::class, $this->producer, \is_string($type) ? $type : get_debug_type($type)));
        }
        unset($headers['type']);

        $outboxEvent = new OutboxEvent(
            Uuid::v7(),
            $type,
            $encoded['body'],
            $headers,
            $this->clock->now(),
        );

        $manager = $this->doctrine->getManagerForClass(OutboxEvent::class)
            ?? throw new \LogicException(\sprintf('No Doctrine entity manager maps %s: add lychen/event-bus\'s src/Entity directory to the API\'s doctrine.orm mappings.', OutboxEvent::class));
        $manager->persist($outboxEvent);

        return $outboxEvent;
    }

    private function typePattern(): string
    {
        return '/^'.preg_quote($this->producer, '/').'\.[a-z][a-z0-9_]*\.[a-z][a-z0-9_]*\.v[1-9][0-9]*$/';
    }
}
