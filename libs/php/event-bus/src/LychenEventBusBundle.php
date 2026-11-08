<?php

namespace Lychen\EventBus;

use Lychen\EventBus\Command\PurgeCommand;
use Lychen\EventBus\Command\RelayCommand;
use Lychen\EventBus\Handler\UnknownEventHandler;
use Lychen\EventBus\Middleware\ForeignMessageGuardMiddleware;
use Lychen\EventBus\Middleware\InboxMiddleware;
use Lychen\EventBus\Outbox\Outbox;
use Lychen\EventBus\Outbox\OutboxRelay;
use Lychen\EventBus\Retry\FixedDelaysRetryStrategy;
use Lychen\EventBus\Serializer\EventSerializer;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * Registers lychen/event-bus's services. It configures nothing of the API's own: the
 * Messenger transport, bus and Doctrine mapping that use these services are spelled out in
 * the API's config files (see the README), like every other shared piece of configuration.
 *
 *     lychen_event_bus:
 *         producer: flora          # the domain this service publishes under
 *         transport: async         # the Messenger transport of its queue
 *         retry_delays: [2000, 10000, 60000]
 *
 * Service ids the API's config refers to:
 *
 * - `lychen_event_bus.serializer`: the transport serializer (EventSerializer).
 * - `lychen_event_bus.middleware.guard`: refuses what another domain may not send, first on the bus.
 * - `lychen_event_bus.middleware.inbox`: the inbox middleware, after `doctrine_transaction`.
 * - `lychen_event_bus.retry_strategy`: the transport's retry strategy (retry_delays).
 *
 * Outbox is autowirable, for the code that records events.
 */
final class LychenEventBusBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('producer')
                    ->info('The domain this service publishes under: the prefix of its event types and routing keys (`flora` for `flora.seed_lot.given.v1`), and the AMQP app_id of its events.')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('transport')
                    ->info('The Messenger transport the relay publishes through: the one consuming the service\'s queue, whose exchange is the platform\'s topic exchange. Its confirm_timeout option must be set.')
                    ->defaultValue('async')
                    ->cannotBeEmpty()
                ->end()
                ->arrayNode('retry_delays')
                    ->info('Milliseconds to wait before each retry of a failed message, for the transport\'s retry_strategy (service lychen_event_bus.retry_strategy). After the last one, the message goes to the failure transport.')
                    ->integerPrototype()->min(0)->end()
                    ->defaultValue([2000, 10000, 60000])
                ->end()
            ->end();
    }

    /**
     * @param array{producer: string, transport: string, retry_delays: list<int>} $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $services = $container->services();
        $logger = service('logger')->nullOnInvalid();

        $services->set(Outbox::class)
            ->args([
                service('doctrine'),
                service('messenger.transport.symfony_serializer'),
                $config['producer'],
                service('clock'),
            ]);

        $services->set('lychen_event_bus.serializer', EventSerializer::class)
            ->args([service('messenger.transport.symfony_serializer')]);

        $services->set('lychen_event_bus.middleware.guard', ForeignMessageGuardMiddleware::class)
            ->args([$config['producer']]);

        $services->set('lychen_event_bus.middleware.inbox', InboxMiddleware::class)
            ->args([service('doctrine'), service('clock'), $logger])
            ->tag('monolog.logger', ['channel' => 'messenger']);

        $services->set('lychen_event_bus.retry_strategy', FixedDelaysRetryStrategy::class)
            ->args([$config['retry_delays']]);

        $services->set(UnknownEventHandler::class)
            ->args([$logger])
            ->tag('messenger.message_handler')
            ->tag('monolog.logger', ['channel' => 'messenger']);

        $services->set(OutboxRelay::class)
            ->args([
                service('doctrine'),
                service('messenger.transport.'.$config['transport']),
                $config['producer'],
                service('clock'),
                $logger,
            ])
            ->tag('monolog.logger', ['channel' => 'messenger']);

        $services->set(RelayCommand::class)
            ->args([service(OutboxRelay::class), $logger])
            ->tag('console.command')
            ->tag('monolog.logger', ['channel' => 'messenger']);

        $services->set(PurgeCommand::class)
            ->args([service('doctrine'), service('clock')])
            ->tag('console.command');
    }
}
