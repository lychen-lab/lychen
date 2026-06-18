<?php

declare(strict_types=1);

namespace App\Poc\Temporal;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

/**
 * Side effects executed by the workflow. In a real integration each method
 * would call the espace API / Doctrine (e.g. update AreaProposal.place, push a
 * notification through Symfony Messenger, (de)index for SEO). Temporal runs
 * them outside the workflow's deterministic context and retries them on
 * failure according to the RetryOptions configured on the stub.
 */
#[ActivityInterface(prefix: 'espace.AreaProposal.')]
interface AreaProposalActivitiesInterface
{
    /** Persist the new place on the AreaProposal entity (the DB write). */
    #[ActivityMethod]
    public function persistPlace(string $uuid, string $place): void;

    #[ActivityMethod]
    public function onEnterPlace(string $uuid, string $place): void;

    #[ActivityMethod]
    public function onLeavePlace(string $uuid, string $place, string $transition): void;

    #[ActivityMethod]
    public function onBlockedTransition(string $uuid, string $place, string $transition): void;

    #[ActivityMethod]
    public function notifyModerators(string $uuid): void;

    #[ActivityMethod]
    public function notifyAuthor(string $uuid, string $message): void;

    #[ActivityMethod]
    public function indexForSeo(string $uuid): void;

    #[ActivityMethod]
    public function removeFromSeoIndex(string $uuid): void;
}
