<?php

namespace App\Event\Incoming\Flora;

use App\Entity\Person;
use App\Entity\SeedStock;
use App\Entity\SeedStockEntry;
use App\Event\Outgoing\SeedStockEntryAdded;
use App\Repository\SeedStockRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Lychen\EventBus\Outbox\Outbox;
use Psr\Clock\ClockInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Adds the lot a flora seed library gave to a member to that member's seed stock.
 *
 * It runs in the worker (`messenger:consume async`), inside the transaction the bus opens for
 * the event (doctrine_transaction), after the inbox has recorded the event as processed:
 *
 *     SELECT person ... FOR UPDATE      -- one stock change per member at a time
 *     INSERT seed_stock (if none), seed_stock_entry
 *     INSERT outbox_event               -- tera.seed_stock.entry_added.v1
 *     COMMIT, then ack
 *
 * An exception rolls all of it back, inbox record included, and the event is retried (2 s,
 * 10 s, 60 s, then the failure transport). A redelivered event never gets here twice: the
 * inbox skips it.
 */
#[AsMessageHandler]
final readonly class SeedLotGivenHandler
{
    /**
     * The stock created for a member who has none yet.
     */
    public const string DEFAULT_STOCK_NAME = 'Mes semences';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private SeedStockRepository $seedStocks,
        private Outbox $outbox,
        private ClockInterface $clock,
        private ?LoggerInterface $logger = null,
    ) {
    }

    public function __invoke(SeedLotGiven $event): void
    {
        $person = null === $event->recipient ? null : $this->lockPerson($event->recipient);
        if (null === $person) {
            // Reserved without being signed in, or by someone with no tera account: there is no
            // stock to add it to, and nothing to retry.
            $this->logger?->info('Seed lot {lot} was given to {recipient}, who has no tera account: nothing to add.', [
                'lot' => (string) $event->seedLot,
                'recipient' => $event->recipient ?? 'an anonymous member',
            ]);

            return;
        }

        $stock = $this->seedStocks->findOneBy(['person' => $person], ['id' => 'ASC']);
        if (null === $stock) {
            $stock = (new SeedStock())
                ->setName(self::DEFAULT_STOCK_NAME)
                ->setPerson($person);
            $this->entityManager->persist($stock);
        }

        $entry = (new SeedStockEntry())
            ->setVariety($event->variety)
            ->setQuantityInGram($event->quantityInGrams)
            ->setReceivedFromSeedLot($event->seedLot);
        $stock->addSeedStockEntry($entry);
        $this->entityManager->persist($entry);

        $this->outbox->record(new SeedStockEntryAdded(
            seedStockEntry: $entry->getUlid(),
            seedStock: $stock->getUlid(),
            owner: (string) $person->getAuthId(),
            variety: $entry->getVariety(),
            quantityInGrams: $entry->getQuantityInGram(),
            receivedFromSeedLot: $entry->getReceivedFromSeedLot(),
            addedAt: $this->clock->now(),
        ));
    }

    /**
     * The member, locked until the transaction ends: two events for the same member (two lots
     * given at once) would otherwise both find no stock and create one each.
     */
    private function lockPerson(string $authId): ?Person
    {
        $person = $this->entityManager->createQueryBuilder()
            ->select('person')
            ->from(Person::class, 'person')
            ->where('person.authId = :authId')
            ->setParameter('authId', $authId)
            ->getQuery()
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)
            ->getOneOrNullResult();

        return $person instanceof Person ? $person : null;
    }
}
