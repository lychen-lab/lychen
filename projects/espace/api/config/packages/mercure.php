<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // The `espace` tenant of the central hub (projects/common/mercure). This API only
    // publishes through it, signing with the tenant's publisher key; browsers subscribe
    // with tokens signed by the subscriber key instead, see MercureSubscriptionProvider.
    $containerConfigurator->extension('mercure', [
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
    ]);
};
