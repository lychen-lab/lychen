<?php

namespace Lychen\EventBus\Middleware;

use Lychen\EventBus\Message\UnknownEvent;
use Lychen\EventBus\Stamp\IntegrationEventStamp;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpReceivedStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * Lets into the service only what another service is allowed to send it: an event of its own
 * domain, whose type is the routing key it was published under.
 *
 * The broker's topic permissions only let a service publish under its own domain's routing
 * keys (`flora.`), but they do not look at the message: with nothing more, flora could publish
 * under `flora.seed_lot.given.v1` a message whose `type` header names one of tera's own
 * classes, and tera would handle it as its own background work. So a message that reached
 * this service under another domain's routing key must carry the event headers, and its type
 * must be that routing key; anything else is refused, without retry.
 *
 * A message under this service's own namespace passes untouched: its background work, and
 * every retry, which comes back under the queue's own name (`tera.events`), its original
 * delivery having been checked already.
 *
 *     'middleware' => ['lychen_event_bus.middleware.guard', 'doctrine_transaction', ...]
 */
final readonly class ForeignMessageGuardMiddleware implements MiddlewareInterface
{
    public function __construct(
        private string $producer,
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $received = $envelope->last(AmqpReceivedStamp::class);
        if (!$received instanceof AmqpReceivedStamp) {
            return $stack->next()->handle($envelope, $stack);
        }

        $routingKey = (string) $received->getAmqpEnvelope()->getRoutingKey();
        if (str_starts_with($routingKey, $this->producer.'.')) {
            return $stack->next()->handle($envelope, $stack);
        }

        $message = $envelope->getMessage();
        $type = $envelope->last(IntegrationEventStamp::class)->type
            ?? ($message instanceof UnknownEvent ? $message->type : null);

        if ($type !== $routingKey) {
            throw new UnrecoverableMessageHandlingException(\sprintf('Refused a message routed under "%s": a message from another domain must be an event whose type is its routing key, got %s.', $routingKey, null === $type ? 'a '.get_debug_type($message).' without event headers' : \sprintf('the type "%s"', $type)));
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
