<?php

declare(strict_types=1);

// The API's tenant of the central hub (projects/common/mercure). An API only publishes
// through it, signing with the tenant's publisher key; browsers subscribe with tokens
// signed by the tenant's subscriber key instead, minted by the API itself.
return [
    'mercure' => [
        'hubs' => [
            'default' => [
                'jwt' => [
                    'publish' => [
                        '*',
                    ],
                    'secret' => '%env(MERCURE_JWT_SECRET)%',
                ],
                'public_url' => '%env(MERCURE_PUBLIC_URL)%',
                'url' => '%env(MERCURE_URL)%',
            ],
        ],
    ],
];
