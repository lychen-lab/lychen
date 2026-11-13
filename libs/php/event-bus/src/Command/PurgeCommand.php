<?php

namespace Lychen\EventBus\Command;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Lychen\EventBus\Entity\OutboxEvent;
use Psr\Clock\ClockInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Deletes what the outbox and the inbox no longer need: published events, and the record of
 * events processed long enough ago that no duplicate of theirs can still arrive.
 *
 * A duplicate comes from a relay or a worker crashing between its commit and the broker's
 * confirm or ack, and arrives within seconds; the inbox default of 30 days leaves room for
 * events replayed by hand from a dead-letter queue. Unpublished events are never deleted.
 */
#[AsCommand(
    name: 'lychen:event-bus:purge',
    description: 'Deletes the published outbox events and the old inbox records',
)]
final class PurgeCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly ClockInterface $clock,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('published-before', null, InputOption::VALUE_REQUIRED, 'Delete the outbox events published before this relative date', '-7 days')
            ->addOption('processed-before', null, InputOption::VALUE_REQUIRED, 'Delete the inbox records of events processed before this relative date', '-30 days');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $now = $this->clock->now();
        $publishedBefore = $now->modify(self::stringOption($input, 'published-before'));
        $processedBefore = $now->modify(self::stringOption($input, 'processed-before'));

        $manager = $this->doctrine->getManagerForClass(OutboxEvent::class);
        if (!$manager instanceof EntityManagerInterface) {
            throw new \LogicException(\sprintf('No Doctrine entity manager maps %s.', OutboxEvent::class));
        }
        $connection = $manager->getConnection();

        $outbox = $connection->executeStatement(
            'DELETE FROM outbox_event WHERE published_at < :before',
            ['before' => $publishedBefore],
            ['before' => Types::DATETIME_IMMUTABLE],
        );
        $inbox = $connection->executeStatement(
            'DELETE FROM processed_event WHERE processed_at < :before',
            ['before' => $processedBefore],
            ['before' => Types::DATETIME_IMMUTABLE],
        );

        $io->success(\sprintf(
            'Deleted %d outbox event(s) published before %s and %d inbox record(s) processed before %s.',
            $outbox,
            $publishedBefore->format(\DATE_RFC3339),
            $inbox,
            $processedBefore->format(\DATE_RFC3339),
        ));

        return Command::SUCCESS;
    }

    private static function stringOption(InputInterface $input, string $name): string
    {
        $value = $input->getOption($name);

        return \is_string($value) ? $value : throw new \InvalidArgumentException(\sprintf('--%s takes a relative date such as "-7 days".', $name));
    }
}
