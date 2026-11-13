<?php

namespace Lychen\EventBus\Command;

use Lychen\EventBus\Outbox\OutboxRelay;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The relay's process: publishes the outbox events as they are committed, until stopped.
 *
 * It drains the outbox batch after batch, then polls it every --sleep milliseconds once it is
 * empty. A failure (broker or database unreachable) is logged and retried with a growing
 * pause, up to 30 seconds; nothing is lost meanwhile, the events wait in the outbox. SIGTERM
 * and SIGINT stop it between two batches, as do --time-limit and --once.
 */
#[AsCommand(
    name: 'lychen:event-bus:relay',
    description: 'Publishes the outbox events to the broker, as they are committed',
)]
final class RelayCommand extends Command implements SignalableCommandInterface
{
    private const int MAX_PAUSE_MS = 30_000;

    private bool $shouldStop = false;

    public function __construct(
        private readonly OutboxRelay $relay,
        private readonly ?LoggerInterface $logger = null,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('batch-size', null, InputOption::VALUE_REQUIRED, 'Events published per transaction', '100')
            ->addOption('sleep', null, InputOption::VALUE_REQUIRED, 'Milliseconds to wait before polling an empty outbox again', '500')
            ->addOption('time-limit', null, InputOption::VALUE_REQUIRED, 'Seconds after which to stop (0: never), for the process manager to start a fresh process', '0')
            ->addOption('once', null, InputOption::VALUE_NONE, 'Publish one batch, then stop');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $batchSize = max(1, self::intOption($input, 'batch-size'));
        $sleepMs = max(0, self::intOption($input, 'sleep'));
        $timeLimit = max(0, self::intOption($input, 'time-limit'));
        $once = (bool) $input->getOption('once');
        $deadline = $timeLimit > 0 ? microtime(true) + $timeLimit : null;
        $failures = 0;

        while (!$this->shouldStop) {
            try {
                $published = $this->relay->relay($batchSize);
                $failures = 0;
            } catch (\Throwable $exception) {
                $pauseMs = min(self::MAX_PAUSE_MS, 1000 * 2 ** min($failures++, 5));
                $this->logger?->error('The outbox relay failed, next attempt in {pause} ms: {message}', ['pause' => $pauseMs, 'message' => $exception->getMessage(), 'exception' => $exception]);
                $output->writeln(\sprintf('<error>%s</error>', $exception->getMessage()));

                if ($once) {
                    return Command::FAILURE;
                }

                $this->pause($pauseMs);

                continue;
            }

            if ($published > 0) {
                $this->logger?->info('Published {count} event(s) from the outbox.', ['count' => $published]);
                $output->writeln(\sprintf('Published %d event(s).', $published), OutputInterface::VERBOSITY_VERBOSE);
            }

            if ($once || (null !== $deadline && microtime(true) >= $deadline)) {
                break;
            }

            // A full batch means more events are probably waiting: go on without pausing.
            if ($published < $batchSize) {
                $this->pause($sleepMs);
            }
        }

        return Command::SUCCESS;
    }

    /**
     * @return list<int>
     */
    public function getSubscribedSignals(): array
    {
        return \defined('SIGTERM') ? [\SIGTERM, \SIGINT] : [];
    }

    public function handleSignal(int $signal, int|false $previousExitCode = 0): int|false
    {
        $this->shouldStop = true;

        // Carry on to the end of the current batch, which then ends the loop.
        return false;
    }

    private static function intOption(InputInterface $input, string $name): int
    {
        $value = $input->getOption($name);

        return \is_string($value) && is_numeric($value) ? (int) $value : throw new \InvalidArgumentException(\sprintf('--%s takes a number, got "%s".', $name, get_debug_type($value)));
    }

    private function pause(int $milliseconds): void
    {
        // In short steps, so that a stop signal is honoured within 100 ms.
        $until = microtime(true) + $milliseconds / 1000;
        while (!$this->shouldStop && microtime(true) < $until) {
            usleep((int) min(100_000, max(0, ($until - microtime(true)) * 1_000_000)));
        }
    }
}
