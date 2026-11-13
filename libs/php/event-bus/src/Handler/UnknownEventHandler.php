<?php

namespace Lychen\EventBus\Handler;

use Lychen\EventBus\Message\UnknownEvent;
use Psr\Log\LoggerInterface;

/**
 * Acknowledges the events this service does not read (see UnknownEvent), so that they leave
 * the queue instead of being retried and failed forever. The warning is the sign that a
 * binding or a class is missing.
 */
final readonly class UnknownEventHandler
{
    public function __construct(
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function __invoke(UnknownEvent $event): void
    {
        $this->logger?->warning('No class of this service declares the event type {type}: event {id} from {producer} is acknowledged without being handled. Declare it with #[AsMessage(serializedTypeName: \'{type}\')], or remove the binding that routes it here.', [
            'type' => $event->type,
            'id' => $event->eventId,
            'producer' => $event->producer ?? 'an unknown producer',
        ]);
    }
}
