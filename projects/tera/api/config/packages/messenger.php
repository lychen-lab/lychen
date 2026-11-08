<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Tests never reach the broker: zenstruck/messenger-test collects whatever is
    // dispatched instead (see InteractsWithMessenger in the test case bases). The
    // options opt into what its 2.0 makes the default: a DelayStamp is honoured, and
    // the transport's own checks leave PHPUnit's assertion count alone.
    $testTransport = 'test://?support_delay_stamp=true&impacts_assertions_count=false';
    $transports = 'test' === $containerConfigurator->env() ? [
        'async' => $testTransport,
        'failed' => $testTransport,
        'sync' => $testTransport,
    ] : [
        // One durable topic exchange for the whole platform, one queue per
        // service. The exchange, the queues, their dead-letter queues and every
        // binding are provisioned by projects/common/rabbitmq — hence
        // auto_setup: false, and hence no binding key or queue argument here:
        // the broker is the only place they are declared.
        //
        // The queue receives tera's own background work and the events of the
        // other services it subscribes to (projects/common/rabbitmq/subscriptions.conf);
        // lychen:event-bus:relay publishes tera's events through this same
        // transport (lychen/event-bus).
        'async' => [
            'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
            'options' => [
                'auto_setup' => false,
                // Wait up to 5 s for the broker to confirm each message it is given:
                // the outbox relay marks an event published only once the broker has
                // it, and a retry is only dropped from the queue once re-published.
                'confirm_timeout' => 5,
                'exchange' => [
                    'name' => 'lychen.events',
                    'type' => 'topic',
                    // Routing keys read `<domain>.<aggregate>.<action>.v<n>`, and
                    // tera may only publish under `tera.`. An event is published under
                    // its own type by the outbox relay; this is the fallback for plain
                    // background work, and it routes back to the tera.events queue.
                    'default_publish_routing_key' => 'tera.internal.message.v1',
                ],
                'queues' => [
                    'tera.events' => [],
                ],
                'delay' => [
                    'exchange_name' => 'lychen.events.delays',
                    'queue_name_pattern' => 'tera.events.delay.%%routing_key%%.%%delay%%',
                    // Messenger would return an expired retry through the default
                    // exchange, which no service is allowed to publish on. Send it
                    // back over the bus instead, on the binding the broker declares
                    // for the queue's own name.
                    'arguments' => [
                        'x-dead-letter-exchange' => 'lychen.events',
                    ],
                ],
            ],
            // Retried after 2 s, 10 s then 60 s (lychen_event_bus.retry_delays), then
            // sent to the failure transport.
            'retry_strategy' => [
                'service' => 'lychen_event_bus.retry_strategy',
            ],
        ],
        // Handler failures that survive every retry. RabbitMQ keeps its own copy
        // in tera.events.dlq — this one is what messenger:failed:* reads, exception
        // included.
        'failed' => 'doctrine://default?queue_name=failed',
        'sync' => 'sync://',
    ];

    $containerConfigurator->extension('framework', [
        'messenger' => [
            'buses' => [
                'messenger.bus.default' => [
                    // A message from another service must be one of its events, typed
                    // after its routing key (lychen/event-bus's guard). Then a message is
                    // handled in one transaction, on a connection checked first (the
                    // worker's may have gone idle), committed before the worker
                    // acknowledges it. An event shares that transaction with its inbox
                    // record (InboxMiddleware), which skips an event already handled,
                    // and with the events its handlers record in the outbox.
                    'middleware' => [
                        'lychen_event_bus.middleware.guard',
                        'doctrine_ping_connection',
                        'doctrine_transaction',
                        'lychen_event_bus.middleware.inbox',
                    ],
                ],
            ],
            'default_bus' => 'messenger.bus.default',
            'failure_transport' => 'failed',
            'serializer' => [
                // JSON, with the `type` header naming the message: the events are read
                // by services that share no PHP code with this one, each mapping the
                // type back to its own class (#[AsMessage(serializedTypeName: ...)]).
                'default_serializer' => 'lychen_event_bus.serializer',
                'symfony_serializer' => [
                    'format' => 'json',
                    'context' => [],
                ],
            ],
            'transports' => $transports,
        ],
    ]);
};
