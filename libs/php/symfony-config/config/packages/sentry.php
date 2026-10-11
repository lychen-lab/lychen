<?php

declare(strict_types=1);

use Sentry\Monolog\Handler;

// The handler service itself is registered by LychenConfigBundle::loadExtension().
return [
    'when@prod' => [
        'monolog' => [
            'handlers' => [
                'sentry' => [
                    'id' => Handler::class,
                    'type' => 'service',
                ],
            ],
        ],
        'sentry' => [
            'dsn' => '%env(SENTRY_DSN)%',
            'register_error_handler' => false,
            'register_error_listener' => false,
        ],
    ],
];
