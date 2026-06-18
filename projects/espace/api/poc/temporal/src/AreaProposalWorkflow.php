<?php

declare(strict_types=1);

namespace App\Poc\Temporal;

use Carbon\CarbonInterval;
use Temporal\Activity\ActivityOptions;
use Temporal\Common\RetryOptions;
use Temporal\Workflow;

/**
 * Durable implementation of the AreaProposal publishing lifecycle.
 *
 * The state machine is intentionally identical to the Symfony one so the two
 * approaches can be compared on equal footing:
 *
 *   draft --submit--> verification --publish--> published --archive--> archived
 *                     verification --reject--> draft
 *                     verification --archive--> archived
 *
 * Differences with the Symfony component:
 *  - The transition table is enforced *here*, in code that survives restarts.
 *  - Each transition triggers Activities (side effects: DB write, notification,
 *    SEO indexing) that Temporal retries automatically on failure.
 *  - A durable timer auto-archives a proposal that stays published for too long
 *    — something that would otherwise require a cron/messenger scheduler.
 */
final class AreaProposalWorkflow implements AreaProposalWorkflowInterface
{
    public const string PLACE_DRAFT = 'draft';
    public const string PLACE_VERIFICATION = 'verification';
    public const string PLACE_PUBLISHED = 'published';
    public const string PLACE_ARCHIVED = 'archived';

    public const string TRANSITION_SUBMIT = 'submit';
    public const string TRANSITION_PUBLISH = 'publish';
    public const string TRANSITION_REJECT = 'reject';
    public const string TRANSITION_ARCHIVE = 'archive';

    /**
     * Transition table: [transition => [from-places => to-place]].
     * Mirrors config/packages/workflow/workflow.php.
     *
     * @var array<string, array{from: list<string>, to: string}>
     */
    private const array TRANSITIONS = [
        self::TRANSITION_SUBMIT => [
            'from' => [self::PLACE_DRAFT],
            'to' => self::PLACE_VERIFICATION,
        ],
        self::TRANSITION_PUBLISH => [
            'from' => [self::PLACE_VERIFICATION],
            'to' => self::PLACE_PUBLISHED,
        ],
        self::TRANSITION_REJECT => [
            'from' => [self::PLACE_VERIFICATION],
            'to' => self::PLACE_DRAFT,
        ],
        self::TRANSITION_ARCHIVE => [
            'from' => [self::PLACE_VERIFICATION, self::PLACE_PUBLISHED],
            'to' => self::PLACE_ARCHIVED,
        ],
    ];

    /**
     * How long a proposal may stay in `published` before it is archived
     * automatically. Demonstrates Temporal durable timers.
     */
    private const string PUBLISHED_TTL = 'P180D';

    private string $place = self::PLACE_DRAFT;

    private ?string $requestedTransition = null;

    private string $rejectReason = '';

    /** @var list<array{transition: string, from: string, to: string}> */
    private array $history = [];

    private readonly AreaProposalActivitiesInterface $activities;

    public function __construct()
    {
        $this->activities = Workflow::newActivityStub(
            AreaProposalActivitiesInterface::class,
            ActivityOptions::new()
                ->withStartToCloseTimeout(CarbonInterval::seconds(30))
                ->withRetryOptions(
                    RetryOptions::new()
                        ->withInitialInterval(CarbonInterval::seconds(1))
                        ->withMaximumAttempts(5),
                ),
        );
    }

    public function run(string $areaProposalUuid): \Generator
    {
        yield $this->activities->onEnterPlace($areaProposalUuid, self::PLACE_DRAFT);

        while (self::PLACE_ARCHIVED !== $this->place) {
            $timedOut = false;

            if (self::PLACE_PUBLISHED === $this->place) {
                // Published proposals wait for an explicit `archive` signal, but
                // are auto-archived once the TTL elapses (durable timer).
                $timedOut = !(yield Workflow::awaitWithTimeout(
                    CarbonInterval::fromString(self::PUBLISHED_TTL),
                    fn (): bool => null !== $this->requestedTransition,
                ));
            } else {
                yield Workflow::await(fn (): bool => null !== $this->requestedTransition);
            }

            $transition = $timedOut ? self::TRANSITION_ARCHIVE : (string) $this->requestedTransition;
            $this->requestedTransition = null;

            if (!$this->canApply($transition)) {
                // Mirrors Symfony's "transition not enabled" — ignored, the
                // workflow keeps waiting for a valid signal.
                yield $this->activities->onBlockedTransition($areaProposalUuid, $this->place, $transition);

                continue;
            }

            yield from $this->applyTransition($areaProposalUuid, $transition);
        }

        return $this->place;
    }

    private function canApply(string $transition): bool
    {
        return isset(self::TRANSITIONS[$transition])
            && \in_array($this->place, self::TRANSITIONS[$transition]['from'], true);
    }

    private function applyTransition(string $uuid, string $transition): \Generator
    {
        $from = $this->place;
        $to = self::TRANSITIONS[$transition]['to'];

        yield $this->activities->onLeavePlace($uuid, $from, $transition);

        $this->place = $to;
        $this->history[] = ['transition' => $transition, 'from' => $from, 'to' => $to];

        // Per-transition side effects, each one a retried Activity.
        switch ($transition) {
            case self::TRANSITION_SUBMIT:
                yield $this->activities->notifyModerators($uuid);
                break;
            case self::TRANSITION_PUBLISH:
                yield $this->activities->indexForSeo($uuid);
                yield $this->activities->notifyAuthor($uuid, 'published');
                break;
            case self::TRANSITION_REJECT:
                yield $this->activities->notifyAuthor($uuid, 'rejected: ' . $this->rejectReason);
                break;
            case self::TRANSITION_ARCHIVE:
                yield $this->activities->removeFromSeoIndex($uuid);
                break;
        }

        yield $this->activities->persistPlace($uuid, $to);
        yield $this->activities->onEnterPlace($uuid, $to);
    }

    public function submit(): void
    {
        $this->requestedTransition = self::TRANSITION_SUBMIT;
    }

    public function publish(): void
    {
        $this->requestedTransition = self::TRANSITION_PUBLISH;
    }

    public function reject(string $reason = ''): void
    {
        $this->rejectReason = $reason;
        $this->requestedTransition = self::TRANSITION_REJECT;
    }

    public function archive(): void
    {
        $this->requestedTransition = self::TRANSITION_ARCHIVE;
    }

    public function getPlace(): string
    {
        return $this->place;
    }

    public function getHistory(): array
    {
        return $this->history;
    }
}
