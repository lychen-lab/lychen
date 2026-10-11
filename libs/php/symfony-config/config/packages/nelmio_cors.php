<?php

declare(strict_types=1);

return [
    'nelmio_cors' => [
        'defaults' => [
            'allow-credentials' => true,
            'allow_headers' => [
                'Content-Type',
                'Authorization',
                'Preload',
                'Fields',
            ],
            'allow_methods' => [
                'GET',
                'OPTIONS',
                'POST',
                'PUT',
                'PATCH',
                'DELETE',
            ],
            'allow_origin' => [
                '%env(CORS_ALLOW_ORIGIN)%',
            ],
            'expose_headers' => [
                'Link',
                'Location',
                'Access-granted',
                'Customer-session-token',
            ],
            'max_age' => 3600,
            'origin_regex' => true,
        ],
        'paths' => [
            '^/' => null,
        ],
    ],
];
