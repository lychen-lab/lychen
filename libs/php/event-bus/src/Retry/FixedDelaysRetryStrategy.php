<?php

namespace Lychen\EventBus\Retry;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Retry\RetryStrategyInterface;
use Symfony\Component\Messenger\Stamp\RedeliveryStamp;

/**
 * Retries a failed message after each of a fixed list of delays, then gives up: with
 * [2000, 10000, 60000], a handler that keeps failing runs 4 times (after 0 s, 2 s, 10 s and
 * 60 s) before the message goes to the failure transport.
 *
 * Messenger's own strategy grows the delay by a constant factor, which cannot express such a
 * schedule. Exceptions marked unrecoverable (UnrecoverableMessageHandlingException) are never
 * retried, whatever the strategy: Messenger checks them first.
 */
final readonly class FixedDelaysRetryStrategy implements RetryStrategyInterface
{
    /**
     * @param list<int> $delays in milliseconds, one per retry
     */
    public function __construct(
        private array $delays,
    ) {
    }

    public function isRetryable(Envelope $message, ?\Throwable $throwable = null): bool
    {
        return RedeliveryStamp::getRetryCountFromEnvelope($message) < \count($this->delays);
    }

    public function getWaitingTime(Envelope $message, ?\Throwable $throwable = null): int
    {
        if ([] === $this->delays) {
            return 0;
        }

        $retry = min(RedeliveryStamp::getRetryCountFromEnvelope($message), \count($this->delays) - 1);

        return $this->delays[$retry];
    }
}
