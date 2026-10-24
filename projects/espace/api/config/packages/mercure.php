<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // The `espace` tenant of the central hub (projects/common/mercure). This API only
    // publishes through it, signing with the tenant's publisher key; browsers subscribe
    // with tokens signed by the subscriber key instead, see MercureSubscriptionProvider.
    // The hub (dunglas/mercure 0.x) speaks the pre-1.0 protocol, which the bundle no
    // longer defaults to since 0.6: keep it in step with the subscriber token factory.
    $containerConfigurator->extension('mercure', [
        'hubs' => [
            'default' => [
                'jwt' => [
                    'publish' => [
                        '*',
                    ],
                    'secret' => '%env(MERCURE_JWT_SECRET)%',
                ],
                'protocol_version' => '0.x',
                'public_url' => '%env(MERCURE_PUBLIC_URL)%',
                'url' => '%env(MERCURE_URL)%',
            ],
        ],
    ]);
};
