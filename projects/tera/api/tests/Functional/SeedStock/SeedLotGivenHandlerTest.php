<?php

namespace App\Tests\Functional\SeedStock;

use App\Entity\SeedStock;
use App\Entity\SeedStockEntry;
use App\Event\Incoming\Flora\SeedLotGiven;
use App\Event\Incoming\Flora\SeedLotGivenHandler;
use App\Factory\PersonFactory;
use App\Factory\SeedStockFactory;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Lychen\EventBus\Entity\OutboxEvent;
use Lychen\EventBus\Entity\ProcessedEvent;
use Lychen\EventBus\Message\UnknownEvent;
use Lychen\EventBus\Stamp\IntegrationEventStamp;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Uid\Uuid;

/**
 * tera's side of `flora.seed_lot.given.v1`: the worker receives the message flora's relay
 * publishes, and handles it once, in one transaction with its inbox record and the event it
 * publishes in turn.
 *
 * Each test hands the async test transport the encoded message as it comes off the queue
 * (body and headers), then runs a real worker on it: the transport serializer decodes it, and
 * the bus's doctrine_transaction and inbox middleware wrap the handler.
 *
 * @see SeedLotGivenHandler
 */
class SeedLotGivenHandlerTest extends AbstractApiTestCase
{
    public function testTheGivenLotIsAddedToTheRecipientsStock(): void
    {
        $person = PersonFactory::createOne(['authId' => 'member-1']);
        $stock = SeedStockFactory::createOne(['name' => 'Potager', 'person' => $person]);
        $seedLot = Uuid::v7();
        $eventId = Uuid::v7();

        $this->receive($eventId, $seedLot, 'member-1');

        $entries = $this->entityManager()->getRepository(SeedStockEntry::class)->findAll();
        self::assertCount(1, $entries);
        self::assertSame($stock->getId(), $entries[0]->getSeedStock()?->getId());
        self::assertSame('Fève', $entries[0]->getVariety());
        self::assertSame(120, $entries[0]->getQuantityInGram());
        self::assertTrue($seedLot->equals($entries[0]->getReceivedFromSeedLot()));

        // Recorded as processed by the inbox, in the same transaction.
        $processed = $this->entityManager()->find(ProcessedEvent::class, $eventId);
        self::assertSame('flora.seed_lot.given.v1', $processed?->getType());

        // And tera's own event, in its outbox, for its relay to publish.
        $outbox = $this->entityManager()->getRepository(OutboxEvent::class)->findAll();
        self::assertCount(1, $outbox);
        self::assertSame('tera.seed_stock.entry_added.v1', $outbox[0]->getType());
        $payload = json_decode($outbox[0]->getPayload(), true, flags: \JSON_THROW_ON_ERROR);
        self::assertSame((string) $entries[0]->getUlid(), $payload['seedStockEntry']);
        self::assertSame('member-1', $payload['owner']);
        self::assertSame((string) $seedLot, $payload['receivedFromSeedLot']);
    }

    public function testARedeliveredEventIsHandledOnce(): void
    {
        PersonFactory::createOne(['authId' => 'member-1']);
        $eventId = Uuid::v7();
        $seedLot = Uuid::v7();

        $this->receive($eventId, $seedLot, 'member-1');
        // The same event again: the relay or the worker crashed before its confirm or ack.
        $this->receive($eventId, $seedLot, 'member-1');

        self::assertCount(1, $this->entityManager()->getRepository(SeedStockEntry::class)->findAll());
        self::assertCount(1, $this->entityManager()->getRepository(ProcessedEvent::class)->findAll());
        self::assertCount(1, $this->entityManager()->getRepository(OutboxEvent::class)->findAll());
        $this->transport('async')->acknowledged()->assertCount(2);
    }

    public function testAMemberWithoutAStockGetsOne(): void
    {
        PersonFactory::createOne(['authId' => 'member-1']);

        $this->receive(Uuid::v7(), Uuid::v7(), 'member-1');

        $stocks = $this->entityManager()->getRepository(SeedStock::class)->findAll();
        self::assertCount(1, $stocks);
        self::assertSame(SeedLotGivenHandler::DEFAULT_STOCK_NAME, $stocks[0]->getName());
        self::assertCount(1, $stocks[0]->getSeedStockEntries());
    }

    public function testALotGivenToSomeoneWithoutATeraAccountChangesNothing(): void
    {
        $eventId = Uuid::v7();

        $this->receive($eventId, Uuid::v7(), 'not-a-tera-member');

        self::assertCount(0, $this->entityManager()->getRepository(SeedStockEntry::class)->findAll());
        self::assertCount(0, $this->entityManager()->getRepository(OutboxEvent::class)->findAll());
        // Handled all the same: a redelivery is skipped.
        self::assertNotNull($this->entityManager()->find(ProcessedEvent::class, $eventId));
        $this->transport('async')->acknowledged()->assertCount(1);
    }

    public function testAnEventTeraDoesNotReadIsAcknowledgedUnhandled(): void
    {
        $this->transport('async')->send([
            'body' => '{"seedLot":"'.Uuid::v7().'"}',
            'headers' => [
                'type' => 'flora.seed_lot.reserved.v1',
                'event_id' => (string) Uuid::v7(),
                'event_producer' => 'flora',
                'Content-Type' => 'application/json',
            ],
        ]);
        $this->transport('async')->throwExceptions()->process();

        $this->transport('async')->acknowledged()->assertContains(UnknownEvent::class, 1);
        self::assertCount(0, $this->entityManager()->getRepository(ProcessedEvent::class)->findAll());
    }

    public function testARetryKeepsTheEventsIdentity(): void
    {
        $serializer = $this->transportSerializer();
        $eventId = Uuid::v7();

        $decoded = $serializer->decode($this->wireMessage($eventId, Uuid::v7(), 'member-1'));
        self::assertInstanceOf(SeedLotGiven::class, $decoded->getMessage());
        self::assertSame((string) $eventId, $decoded->last(IntegrationEventStamp::class)?->eventId);

        // What a retry, or the failure transport, encodes again.
        $reencoded = $serializer->encode($decoded);
        self::assertSame('flora.seed_lot.given.v1', $reencoded['headers']['type']);
        self::assertSame((string) $eventId, $reencoded['headers']['event_id']);
        self::assertSame((string) $eventId, $serializer->decode($reencoded)->last(IntegrationEventStamp::class)?->eventId);
    }

    /**
     * Has the worker receive the event as flora's relay publishes it.
     */
    private function receive(Uuid $eventId, Uuid $seedLot, ?string $recipient): void
    {
        $this->transport('async')->send($this->wireMessage($eventId, $seedLot, $recipient));
        $this->transport('async')->throwExceptions()->process();
        $this->entityManager()->clear();
    }

    /**
     * @return array{body: string, headers: array<string, string>}
     */
    private function wireMessage(Uuid $eventId, Uuid $seedLot, ?string $recipient): array
    {
        return [
            'body' => json_encode([
                'seedLot' => (string) $seedLot,
                'seedLibrary' => (string) Uuid::v7(),
                'variety' => 'Fève',
                'harvestYear' => 2025,
                'quantityInGrams' => 120,
                'recipient' => $recipient,
                'givenAt' => '2026-11-08T10:00:00+00:00',
            ], \JSON_THROW_ON_ERROR),
            'headers' => [
                'type' => 'flora.seed_lot.given.v1',
                'event_id' => (string) $eventId,
                'event_producer' => 'flora',
                'event_occurred_at' => '2026-11-08T10:00:00+00:00',
                'Content-Type' => 'application/json',
            ],
        ];
    }

    private function entityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }

    private function transportSerializer(): SerializerInterface
    {
        return self::getContainer()->get('lychen_event_bus.serializer');
    }
}
