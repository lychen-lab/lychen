<?php

declare(strict_types=1);

return [
    'snc_redis' => [
        'clients' => [
            'default' => [
                'alias' => 'default',
                'dsn' => '%env(REDIS_URL)%',
                'logging' => true,
                'type' => 'predis',
            ],
        ],
    ],
];
