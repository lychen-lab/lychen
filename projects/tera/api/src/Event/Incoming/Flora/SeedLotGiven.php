<?php

namespace App\Event\Incoming\Flora;

use Symfony\Component\Messenger\Attribute\AsMessage;
use Symfony\Component\Uid\Uuid;

/**
 * tera's copy of flora's `flora.seed_lot.given.v1`: a seed library gave a reserved lot to the
 * member who reserved it. Handled by SeedLotGivenHandler.
 *
 * The services share the wire format, not this class: the serialized type name maps the
 * event's `type` header to it, and its properties read the JSON body. It declares only the
 * fields tera reads: the others (seedLibrary, harvestYear, givenAt), and any field flora adds
 * later, are ignored. tera receives it because its queue is bound to that routing key
 * (projects/common/rabbitmq/subscriptions.conf).
 */
#[AsMessage(serializedTypeName: 'flora.seed_lot.given.v1')]
final readonly class SeedLotGiven
{
    public function __construct(
        public Uuid $seedLot,
        public string $variety,
        public int $quantityInGrams,
        /**
         * The Zitadel user id of the member who received the lot: Person::$authId in tera.
         */
        public ?string $recipient,
    ) {
    }
}
