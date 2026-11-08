<?php

namespace Lychen\EventBus\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

/**
 * The inbox: one row per event this service has handled, written by InboxMiddleware in the
 * same transaction as the handler's changes.
 *
 * The primary key on the event id is what makes a redelivered event a no-op: its second
 * INSERT hits the key and the handlers are skipped. Mapped so that the API's migrations and
 * doctrine:schema:validate know the table; the middleware writes it with plain SQL
 * (INSERT ... ON CONFLICT DO NOTHING), hence the pinned column names.
 */
#[ORM\Entity]
#[ORM\Table(name: 'processed_event')]
#[ORM\Index(name: 'processed_event_processed_at_idx', columns: ['processed_at'])]
class ProcessedEvent
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(name: 'event_id', type: UuidType::NAME)]
        private Uuid $eventId,
        #[ORM\Column(name: 'type', length: 255)]
        private string $type,
        #[ORM\Column(name: 'processed_at', type: Types::DATETIME_IMMUTABLE)]
        private \DateTimeImmutable $processedAt,
    ) {
    }

    public function getEventId(): Uuid
    {
        return $this->eventId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProcessedAt(): \DateTimeImmutable
    {
        return $this->processedAt;
    }
}
