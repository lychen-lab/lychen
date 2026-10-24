<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // The hub (Mercure 1.0) takes OAuth 2.0 access tokens (RFC 9068), which name their
    // issuer, subject and client. `iss` has to be the issuer the `espace` tenant of the hub
    // trusts (see projects/common/mercure/entrypoint.sh); the API is the client, and the
    // subject of its own tokens. `aud` is left to the bundle: the hub's public URL, which
    // is also the resource identifier the tenant pins.
    $claims = [
        'client_id' => 'urn:lychen:espace-api',
        'iss' => 'urn:lychen:espace-api',
        'sub' => 'urn:lychen:espace-api',
    ];

    // The `espace` tenant of the central hub (projects/common/mercure). This API publishes
    // with the tenant's publisher key; browsers subscribe with tokens signed by its
    // subscriber key instead, so a token handed to a browser can never publish. Those are
    // minted by the hub's token factory, see MercureSubscriptionProvider.
    $containerConfigurator->extension('mercure', [
        'hubs' => [
            'default' => [
                'public_url' => '%env(MERCURE_PUBLIC_URL)%',
                'publisher' => [
                    'claims' => $claims,
                    'secret' => '%env(MERCURE_JWT_SECRET)%',
                    'topics' => [
                        '*',
                    ],
                ],
                'subscriber' => [
                    'claims' => $claims,
                    'secret' => '%env(MERCURE_SUBSCRIBER_JWT_SECRET)%',
                ],
                'url' => '%env(MERCURE_URL)%',
            ],
        ],
    ]);
};
