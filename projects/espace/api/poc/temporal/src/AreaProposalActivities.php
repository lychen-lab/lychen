<?php

declare(strict_types=1);

namespace App\Poc\Temporal;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * POC implementation of the activities: it only logs what it would do.
 *
 * In the espace API these methods would be plain Symfony services, autowired
 * with the EntityManager, MessageBus, etc. Replacing the bodies below with real
 * calls is all that stands between this POC and a production integration:
 *
 *   public function persistPlace(string $uuid, string $place): void
 *   {
 *       $proposal = $this->repository->findOneByUuid($uuid);
 *       $proposal->setPlace($place);
 *       $this->em->flush();
 *   }
 */
final class AreaProposalActivities implements AreaProposalActivitiesInterface
{
    public function __construct(
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {
    }

    public function persistPlace(string $uuid, string $place): void
    {
        $this->log($uuid, sprintf('persist place = %s', $place));
    }

    public function onEnterPlace(string $uuid, string $place): void
    {
        $this->log($uuid, sprintf('entered place "%s"', $place));
    }

    public function onLeavePlace(string $uuid, string $place, string $transition): void
    {
        $this->log($uuid, sprintf('leaving place "%s" via transition "%s"', $place, $transition));
    }

    public function onBlockedTransition(string $uuid, string $place, string $transition): void
    {
        $this->log($uuid, sprintf('transition "%s" not allowed from place "%s" — ignored', $transition, $place));
    }

    public function notifyModerators(string $uuid): void
    {
        $this->log($uuid, 'notify moderators: a proposal is awaiting verification');
    }

    public function notifyAuthor(string $uuid, string $message): void
    {
        $this->log($uuid, sprintf('notify author: %s', $message));
    }

    public function indexForSeo(string $uuid): void
    {
        $this->log($uuid, 'index proposal for SEO');
    }

    public function removeFromSeoIndex(string $uuid): void
    {
        $this->log($uuid, 'remove proposal from SEO index');
    }

    private function log(string $uuid, string $message): void
    {
        $line = sprintf('[AreaProposal %s] %s', $uuid, $message);
        $this->logger->info($line);
        fwrite(\STDERR, $line . \PHP_EOL);
    }
}
