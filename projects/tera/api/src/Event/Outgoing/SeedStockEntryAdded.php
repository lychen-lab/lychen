<?php

namespace App\Event\Outgoing;

use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Uid\Uuid;

/**
 * Published when seeds enter a member's seed stock, for now when a seed library gives them a
 * lot (see SeedLotGivenHandler). Recorded in tera's outbox and published by tera's relay under
 * `tera.seed_stock.entry_added.v1`, to whichever service subscribes to it.
 */
#[AsMessage(serializedTypeName: 'tera.seed_stock.entry_added.v1')]
final readonly class SeedStockEntryAdded
{
    public function __construct(
        public Ulid $seedStockEntry,
        public Ulid $seedStock,
        /**
         * The Zitadel user id of the stock's owner.
         */
        public string $owner,
        public ?string $variety,
        public ?int $quantityInGrams,
        /**
         * The flora seed lot the seeds came from, if they came from a seed library.
         */
        public ?Uuid $receivedFromSeedLot,
        public \DateTimeImmutable $addedAt,
    ) {
    }
}
