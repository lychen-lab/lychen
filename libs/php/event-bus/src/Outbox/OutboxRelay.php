<?php

namespace Lychen\EventBus\Outbox;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\ForUpdate\ConflictResolutionMode;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Lychen\EventBus\Entity\OutboxEvent;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;

/**
 * Publishes the committed outbox events to the broker, oldest first, in a process of its own
 * (`lychen:event-bus:relay`): the broker's latency and availability never reach the request
 * that recorded the event.
 *
 * One batch is one transaction:
 *
 *     SELECT ... FROM outbox_event WHERE published_at IS NULL ORDER BY id LIMIT n
 *         FOR UPDATE SKIP LOCKED                       -- several relays share the rows
 *     publish, wait for the broker's confirm           -- per event
 *     UPDATE outbox_event SET published_at = now()     -- per confirmed event
 *     COMMIT
 *
 * Publish, mark, commit: a crash between the confirm and the commit publishes the event again
 * on the next batch, so a consumer may see an event twice, never zero times. Consumers
 * deduplicate on the event id (InboxMiddleware).
 *
 * The confirm is the transport's: its `confirm_timeout` option must be set, or the sender
 * returns before the broker has the message and an event could be marked published and lost.
 * The relay publishes through the Messenger transport that consumes the service's own queue,
 * which already holds the broker's address, credentials and exchange: each event goes to the
 * exchange under its type as routing key, and the broker's bindings route it to the queue of
 * every service that subscribes to it.
 */
final readonly class OutboxRelay
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private SenderInterface $sender,
        private string $producer,
        private ClockInterface $clock,
        private ?LoggerInterface $logger = null,
    ) {
    }

    /**
     * Publishes up to $limit pending events, and answers how many were published.
     *
     * A failure to publish stops the batch: the events confirmed before it stay published,
     * the others wait for the next batch, and the failure is thrown once that is committed.
     */
    public function relay(int $limit = 100): int
    {
        $connection = $this->connection();
        $published = 0;
        $failure = null;
        $failedEvent = null;

        $connection->beginTransaction();
        try {
            $rows = $connection->createQueryBuilder()
                ->select('id', 'type', 'payload', 'headers', 'occurred_at')
                ->from('outbox_event')
                ->where('published_at IS NULL')
                ->orderBy('id')
                ->setMaxResults($limit)
                ->forUpdate(ConflictResolutionMode::SKIP_LOCKED)
                ->fetchAllAssociative();

            foreach ($rows as $row) {
                $message = $this->message($row);

                try {
                    $this->sender->send($this->envelope($message));
                } catch (\Throwable $exception) {
                    $failure = $exception;
                    $failedEvent = $message;
                    break;
                }

                $connection->update(
                    'outbox_event',
                    ['published_at' => $this->clock->now()],
                    ['id' => $message->id],
                    ['published_at' => Types::DATETIME_IMMUTABLE],
                );
                ++$published;

                $this->logger?->debug('Published event {id} ({type}).', ['id' => $message->id, 'type' => $message->type]);
            }

            $connection->commit();
        } catch (\Throwable $exception) {
            $this->abandon($connection);

            throw $exception;
        }

        if (null !== $failure) {
            throw new \RuntimeException(\sprintf('Could not publish event %s (%s), after %d published in this batch: %s', $failedEvent?->id, $failedEvent?->type, $published, $failure->getMessage()), 0, $failure);
        }

        return $published;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function message(array $row): OutboxMessage
    {
        $headers = [];
        foreach ((array) json_decode(self::string($row, 'headers'), true, flags: \JSON_THROW_ON_ERROR) as $name => $value) {
            if (\is_string($name) && \is_string($value)) {
                $headers[$name] = $value;
            }
        }

        return new OutboxMessage(
            self::string($row, 'id'),
            self::string($row, 'type'),
            self::string($row, 'payload'),
            $headers,
            new \DateTimeImmutable(self::string($row, 'occurred_at')),
            $this->producer,
        );
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function string(array $row, string $column): string
    {
        $value = $row[$column] ?? null;

        return \is_string($value) ? $value : throw new \UnexpectedValueException(\sprintf('outbox_event.%s is not a string: %s.', $column, get_debug_type($value)));
    }

    private function envelope(OutboxMessage $message): Envelope
    {
        // The routing key is the event type: the broker routes on it. The AMQP properties
        // describe the message to the broker's tooling (management UI, tracing); consumers read
        // the headers EventSerializer writes.
        return new Envelope($message, [
            new AmqpStamp($message->type, \AMQP_NOPARAM, [
                'app_id' => $message->producer,
                'message_id' => $message->id,
                'timestamp' => $message->occurredAt->getTimestamp(),
                'type' => $message->type,
            ]),
        ]);
    }

    private function connection(): Connection
    {
        $manager = $this->doctrine->getManagerForClass(OutboxEvent::class);

        return $manager instanceof EntityManagerInterface
            ? $manager->getConnection()
            : throw new \LogicException(\sprintf('No Doctrine entity manager maps %s: add lychen/event-bus\'s src/Entity directory to the API\'s doctrine.orm mappings.', OutboxEvent::class));
    }

    /**
     * Rolls back what is left of the batch and drops the connection, which the next batch
     * opens again: a failure here is often the database going away, and a DBAL connection does
     * not reconnect by itself once broken.
     */
    private function abandon(Connection $connection): void
    {
        try {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }
        } catch (\Throwable) {
            // The connection is already gone: closing it below is all that is left to do.
        }

        $connection->close();
    }
}
