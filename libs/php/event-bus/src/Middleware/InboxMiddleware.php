<?php

namespace Lychen\EventBus\Middleware;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Lychen\EventBus\Entity\ProcessedEvent;
use Lychen\EventBus\Stamp\IntegrationEventStamp;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

/**
 * The inbox: handles an integration event at most once per service, however many times the
 * broker delivers it.
 *
 * It runs inside the transaction of Doctrine's `doctrine_transaction` middleware, which must
 * come first on the bus:
 *
 *     'middleware' => ['doctrine_transaction', 'lychen_event_bus.middleware.inbox']
 *
 * so that, for each received event:
 *
 *     BEGIN                                            -- doctrine_transaction
 *     INSERT INTO processed_event ... ON CONFLICT (event_id) DO NOTHING
 *       -> 0 row: already processed, the handlers are skipped
 *       -> 1 row: the handlers run (their changes, and the events they record in the outbox)
 *     COMMIT                                           -- doctrine_transaction
 *     ack                                              -- the worker, once committed
 *
 * A handler that throws rolls the whole transaction back, its processed_event row included,
 * so the retry handles the event again. A crash between the commit and the ack makes the
 * broker deliver the event again, and that second delivery hits the primary key. Two workers
 * receiving the same event at once serialize on that key: the second waits for the first to
 * commit (then skips) or roll back (then handles it).
 *
 * Only messages received from a transport and carrying an IntegrationEventStamp are concerned.
 */
final readonly class InboxMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private ClockInterface $clock,
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $event = $envelope->last(IntegrationEventStamp::class);
        if (!$event instanceof IntegrationEventStamp || null === $envelope->last(ReceivedStamp::class)) {
            return $stack->next()->handle($envelope, $stack);
        }

        $connection = $this->connection();
        if (!$connection->isTransactionActive()) {
            throw new \LogicException(\sprintf('%s must run inside a transaction: put the "doctrine_transaction" middleware before it on the bus.', self::class));
        }

        $recorded = $connection->executeStatement(
            'INSERT INTO processed_event (event_id, type, processed_at) VALUES (:id, :type, :processedAt) ON CONFLICT (event_id) DO NOTHING',
            ['id' => $event->eventId, 'type' => $event->type, 'processedAt' => $this->clock->now()],
            ['processedAt' => Types::DATETIME_IMMUTABLE],
        );

        if (0 === $recorded) {
            $this->logger?->info('Event {id} ({type}) was already processed: skipped.', ['id' => $event->eventId, 'type' => $event->type]);

            return $envelope;
        }

        return $stack->next()->handle($envelope, $stack);
    }

    private function connection(): Connection
    {
        $manager = $this->doctrine->getManagerForClass(ProcessedEvent::class);

        return $manager instanceof EntityManagerInterface
            ? $manager->getConnection()
            : throw new \LogicException(\sprintf('No Doctrine entity manager maps %s: add lychen/event-bus\'s src/Entity directory to the API\'s doctrine.orm mappings.', ProcessedEvent::class));
    }
}
