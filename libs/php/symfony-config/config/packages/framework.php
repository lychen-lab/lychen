<?php

declare(strict_types=1);

/** @var string $env the kernel environment, which the PHP loader exposes to config files */

// Tests never reach the broker: zenstruck/messenger-test collects whatever is
// dispatched instead (see InteractsWithMessenger in the test case bases).
$transports = 'test' === $env ? [
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
                // a service may only publish under its own domain. A message that
                // carries a domain event sets its own key through an AmqpStamp;
                // this is the fallback for plain background work, and it routes
                // back to the service's own queue.
                'default_publish_routing_key' => '%lychen.service%.internal.message.v1',
            ],
            'queues' => [
                '%lychen.service%.events' => [],
            ],
            'delay' => [
                'exchange_name' => 'lychen.events.delays',
                'queue_name_pattern' => '%lychen.service%.events.delay.%%routing_key%%.%%delay%%',
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
    // in <service>.events.dlq — this one is what messenger:failed:* reads,
    // exception included.
    'failed' => 'doctrine://default?queue_name=failed',
    'sync' => 'sync://',
];

return [
    'framework' => [
        'assets' => null,
        'cache' => [
            'app' => 'cache.adapter.redis',
            'default_redis_provider' => '%env(resolve:REDIS_URL)%',
        ],
        'default_locale' => 'fr',
        'disallow_search_engine_index' => true,
        'handle_all_throwables' => true,
        'http_method_override' => false,
        'mailer' => [
            'dsn' => '%env(MAILER_DSN)%',
        ],
        'messenger' => [
            'failure_transport' => 'failed',
            'transports' => $transports,
        ],
        'php_errors' => [
            'log' => true,
        ],
        'router' => null,
        'secret' => '%env(APP_SECRET)%',
        'translator' => [
            'default_path' => '%kernel.project_dir%/translations',
            'fallbacks' => [
                'fr',
            ],
        ],
        'trusted_proxies' => '127.0.0.1,REMOTE_ADDR',
        'validation' => [
            'email_validation_mode' => 'strict',
        ],
    ],
    'when@dev' => [
        'framework' => [
            'router' => [
                'strict_requirements' => true,
            ],
        ],
    ],
    'when@prod' => [
        'framework' => [
            'cache' => [
                'default_redis_provider' => 'snc_redis.default',
                'app' => 'cache.adapter.redis',
                'system' => 'cache.adapter.redis',
            ],
            'router' => [
                'strict_requirements' => null,
            ],
        ],
    ],
    'when@test' => [
        'framework' => [
            'router' => [
                'strict_requirements' => true,
            ],
            'test' => true,
            'validation' => [
                'not_compromised_password' => false,
            ],
        ],
    ],
];
