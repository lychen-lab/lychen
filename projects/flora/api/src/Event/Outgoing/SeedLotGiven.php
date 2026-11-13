<?php

namespace App\Event\Outgoing;

use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Uid\Uuid;

/**
 * Published when a seed library gives a reserved lot to the member who reserved it: the
 * "give" transition of SeedLotWorkflow (see SeedLotWorkflowSubscriber).
 *
 * Its serialized type name is its routing key on the platform's topic exchange: the broker
 * delivers it to the queue of every service bound to `flora.seed_lot.given.v1` (see
 * projects/common/rabbitmq/subscriptions.conf). This class is flora's side of the contract;
 * a consumer declares its own copy. On the wire:
 *
 *     headers: type=flora.seed_lot.given.v1, event_id=<uuid>, event_producer=flora, ...
 *     body:    {"seedLot":"0199...","seedLibrary":"0199...","variety":"Fève","harvestYear":2025,
 *               "quantityInGrams":120,"recipient":"312987...","givenAt":"2026-11-08T10:00:00+00:00"}
 *
 * A change that breaks consumers (a field removed, renamed or retyped) is a new version,
 * `flora.seed_lot.given.v2`, published alongside v1 until no service reads v1 any more.
 */
#[AsMessage(serializedTypeName: 'flora.seed_lot.given.v1')]
final readonly class SeedLotGiven
{
    public function __construct(
        public Uuid $seedLot,
        public Uuid $seedLibrary,
        public string $variety,
        public ?int $harvestYear,
        public int $quantityInGrams,
        /**
         * The Zitadel user id of the member who received the lot, null when it was reserved
         * without being signed in.
         */
        public ?string $recipient,
        public \DateTimeImmutable $givenAt,
    ) {
    }
}
