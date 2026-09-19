<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Tests never reach the broker: zenstruck/messenger-test collects whatever is
    // dispatched instead (see InteractsWithMessenger in the test case bases).
    $transports = 'test' === $containerConfigurator->env() ? [
        'async' => 'test://',
        'failed' => 'test://',
        'sync' => 'test://',
    ] : [
        // One durable topic exchange for the whole platform, one queue per
        // service. The exchange, the queues, their dead-letter queues and every
        // binding are provisioned by projects/common/rabbitmq — hence
        // auto_setup: false, and hence no binding key or queue argument here:
        // the broker is the only place they are declared.
        'async' => [
            'dsn' => '%env(MESSENGER_TRANSPORT_DSN)%',
            'options' => [
                'auto_setup' => false,
                'exchange' => [
                    'name' => 'lychen.events',
                    'type' => 'topic',
                    // Routing keys read `<domain>.<aggregate>.<action>.v<n>`, and
                    // espace may only publish under `espace.`. A message that carries a
                    // domain event sets its own key through an AmqpStamp; this is
                    // the fallback for plain background work, and it routes back
                    // to the espace.events queue.
                    'default_publish_routing_key' => 'espace.internal.message.v1',
                ],
                'queues' => [
                    'espace.events' => [],
                ],
                'delay' => [
                    'exchange_name' => 'lychen.events.delays',
                    'queue_name_pattern' => 'espace.events.delay.%%routing_key%%.%%delay%%',
                    // Messenger would return an expired retry through the default
                    // exchange, which no service is allowed to publish on. Send it
                    // back over the bus instead, on the binding the broker declares
                    // for the queue's own name.
                    'arguments' => [
                        'x-dead-letter-exchange' => 'lychen.events',
                    ],
                ],
            ],
        ],
        // Handler failures that survive every retry. RabbitMQ keeps its own copy
        // in espace.events.dlq — this one is what messenger:failed:* reads, exception
        // included.
        'failed' => 'doctrine://default?queue_name=failed',
        'sync' => 'sync://',
    ];

    $containerConfigurator->extension('framework', [
        'messenger' => [
            'failure_transport' => 'failed',
            'transports' => $transports,
        ],
    ]);
};
