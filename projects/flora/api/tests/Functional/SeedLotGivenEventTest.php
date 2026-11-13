<?php

namespace App\Tests\Functional;

use App\Entity\SeedLot;
use App\Event\Outgoing\SeedLotGiven;
use App\Factory\SeedLotFactory;
use App\Workflow\SeedLot\SeedLotWorkflow;
use App\Workflow\SeedLot\SeedLotWorkflowSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Lychen\EventBus\Entity\OutboxEvent;
use Lychen\EventBus\Outbox\Outbox;
use Lychen\EventBus\Outbox\OutboxMessage;
use Lychen\EventBus\Outbox\OutboxRelay;
use Lychen\Test\AbstractApiTestCase;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * flora's side of `flora.seed_lot.given.v1`: the give transition records the event in the
 * outbox, in its own transaction, and the relay publishes it once.
 *
 * @see SeedLotWorkflowSubscriber
 * @see OutboxRelay
 */
class SeedLotGivenEventTest extends AbstractApiTestCase
{
    public function testGivingALotRecordsTheEventWithoutPublishingIt(): void
    {
        $lot = $this->reservedLot('member-1');

        $this->browser()
            ->patch('/api/seed_lots/'.$lot->getUuid().'/'.SeedLotWorkflow::TRANSITION_GIVE)
            ->assertStatus(200)
            ->assertJsonMatches('state', SeedLotWorkflow::PLACE_GIVEN);

        $events = $this->outboxEvents();
        self::assertCount(1, $events);
        self::assertSame('flora.seed_lot.given.v1', $events[0]->getType());
        self::assertNull($events[0]->getPublishedAt());
        self::assertSame('application/json', $events[0]->getHeaders()['Content-Type'] ?? null);

        self::assertSame([
            'seedLot' => (string) $lot->getUuid(),
            'seedLibrary' => (string) $lot->getSeedLibrary()->getUuid(),
            'variety' => 'Fève',
            'harvestYear' => 2025,
            'quantityInGrams' => 120,
            'recipient' => 'member-1',
        ], array_diff_key(json_decode($events[0]->getPayload(), true, flags: \JSON_THROW_ON_ERROR), ['givenAt' => true]));

        // Published by the relay, later: nothing reached the transport during the request.
        $this->transport('async')->queue()->assertEmpty();
    }

    public function testARefusedTransitionRecordsNothing(): void
    {
        $lot = SeedLotFactory::createOne();

        // An available lot must be reserved before it is given.
        $this->browser()
            ->patch('/api/seed_lots/'.$lot->getUuid().'/'.SeedLotWorkflow::TRANSITION_GIVE)
            ->assertStatus(409);

        self::assertSame([], $this->outboxEvents());
    }

    public function testTheRelayPublishesEachEventOnceUnderItsType(): void
    {
        $lot = $this->reservedLot('member-1');
        $this->browser()
            ->patch('/api/seed_lots/'.$lot->getUuid().'/'.SeedLotWorkflow::TRANSITION_GIVE)
            ->assertStatus(200);
        [$recorded] = $this->outboxEvents();

        $relay = self::getContainer()->get(OutboxRelay::class);
        self::assertSame(1, $relay->relay());
        // Already published: the next batch has nothing to send.
        self::assertSame(0, $relay->relay());

        $this->transport('async')->queue()->assertCount(1);
        $envelope = $this->transport('async')->queue()->first()->envelope;
        $amqpStamp = $envelope->last(AmqpStamp::class);
        self::assertInstanceOf(AmqpStamp::class, $amqpStamp);
        self::assertSame('flora.seed_lot.given.v1', $amqpStamp->getRoutingKey());
        self::assertSame((string) $recorded->getId(), $amqpStamp->getAttributes()['message_id'] ?? null);

        // On the wire: the payload as recorded, byte for byte, and the event's headers.
        $message = $envelope->getMessage();
        self::assertInstanceOf(OutboxMessage::class, $message);
        $encoded = $this->transportSerializer()->encode($envelope);
        self::assertSame($recorded->getPayload(), $encoded['body']);
        self::assertSame('flora.seed_lot.given.v1', $encoded['headers']['type']);
        self::assertSame((string) $recorded->getId(), $encoded['headers']['event_id']);
        self::assertSame('flora', $encoded['headers']['event_producer']);

        $this->entityManager()->clear();
        $published = $this->entityManager()->find(OutboxEvent::class, $recorded->getId());
        self::assertNotNull($published?->getPublishedAt());
    }

    public function testReservingRemembersTheSignedInMemberAndReleasingForgetsThem(): void
    {
        $lot = SeedLotFactory::createOne();
        self::getContainer()->get('security.token_storage')
            ->setToken(new UsernamePasswordToken(new InMemoryUser('member-42', null), 'main'));

        $this->seedLotWorkflow()->apply($lot, SeedLotWorkflow::TRANSITION_RESERVE);
        self::assertSame('member-42', $lot->getReservedBy());

        $this->seedLotWorkflow()->apply($lot, SeedLotWorkflow::TRANSITION_RELEASE);
        self::assertNull($lot->getReservedBy());
    }

    public function testAnEventMustBeTypedUnderFlorasRoutingKeys(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('#[AsMessage(serializedTypeName: \'flora.<aggregate>.<action>.v<n>\')]');

        self::getContainer()->get(Outbox::class)->record(new \stdClass());
    }

    public function testTheEventClassDeclaresItsRoutingKey(): void
    {
        $encoded = $this->transportSerializer()->encode(new Envelope(new SeedLotGiven(
            seedLot: Uuid::v7(),
            seedLibrary: Uuid::v7(),
            variety: 'Fève',
            harvestYear: null,
            quantityInGrams: 10,
            recipient: null,
            givenAt: new \DateTimeImmutable(),
        )));

        self::assertSame('flora.seed_lot.given.v1', $encoded['headers']['type']);
    }

    private function reservedLot(string $member): SeedLot
    {
        return SeedLotFactory::createOne([
            'variety' => 'Fève',
            'harvestYear' => 2025,
            'quantityInGrams' => 120,
            'state' => SeedLotWorkflow::PLACE_RESERVED,
            'reservedBy' => $member,
        ]);
    }

    /**
     * @return list<OutboxEvent>
     */
    private function outboxEvents(): array
    {
        $this->entityManager()->clear();

        return $this->entityManager()->getRepository(OutboxEvent::class)->findBy([], ['id' => 'ASC']);
    }

    private function entityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }

    private function transportSerializer(): SerializerInterface
    {
        return self::getContainer()->get('lychen_event_bus.serializer');
    }

    private function seedLotWorkflow(): WorkflowInterface
    {
        return self::getContainer()->get('state_machine.'.SeedLotWorkflow::NAME);
    }
}
