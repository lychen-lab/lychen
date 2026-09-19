<?php

namespace App\Doctrine\Listener;

use App\Api\Resource\AreaProposal\AreaProposal as AreaProposalResource;
use App\Entity\AreaProposal;
use App\Mercure\ResourceUpdatePublisher;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Pushes every change to an area proposal to the Mercure hub, once it is committed.
 *
 * Hooked on Doctrine rather than on the API processors so that any write is published:
 * the API's, but also those of fixtures, commands and — once they exist — the Temporal
 * activities that move a proposal through its workflow.
 */
#[AsEntityListener(event: Events::postPersist, method: 'onWrite', entity: AreaProposal::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'onWrite', entity: AreaProposal::class)]
#[AsEntityListener(event: Events::postRemove, method: 'onRemove', entity: AreaProposal::class)]
#[AsDoctrineListener(event: Events::postFlush)]
final class AreaProposalMercureListener implements ResetInterface
{
    /** @var array<string, AreaProposal> written during the flush in progress, by UUID */
    private array $written = [];

    /** @var array<string, Uuid> removed during the flush in progress, by UUID */
    private array $removed = [];

    public function __construct(
        private readonly ResourceUpdatePublisher $publisher,
        private readonly ObjectMapperInterface $objectMapper,
    ) {
    }

    public function onWrite(AreaProposal $areaProposal): void
    {
        $this->written[(string) $areaProposal->getUuid()] = $areaProposal;
    }

    public function onRemove(AreaProposal $areaProposal): void
    {
        $uuid = $areaProposal->getUuid();
        unset($this->written[(string) $uuid]);
        $this->removed[(string) $uuid] = $uuid;
    }

    /**
     * The post* events above fire inside the flush's transaction, so publishing from them
     * would announce changes a rollback can still undo. postFlush comes after the commit.
     */
    public function postFlush(): void
    {
        [$written, $removed] = [$this->written, $this->removed];
        $this->reset();

        foreach ($written as $areaProposal) {
            $this->publisher->publish($this->objectMapper->map($areaProposal, AreaProposalResource::class));
        }

        foreach ($removed as $uuid) {
            $this->publisher->publishDeletion(AreaProposalResource::class, ['uuid' => (string) $uuid]);
        }
    }

    public function reset(): void
    {
        $this->written = [];
        $this->removed = [];
    }
}
