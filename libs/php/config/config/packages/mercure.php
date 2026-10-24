<?php

declare(strict_types=1);

use Lychen\Config\Test\MercureHubStub;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    // The hub (Mercure 1.0) takes OAuth 2.0 access tokens (RFC 9068), which name their
    // issuer, subject and client. `iss` has to be the issuer the API's tenant of the hub
    // trusts, `urn:lychen:<service>-api` (see projects/common/mercure/entrypoint.sh); the
    // API is the client, and the subject of its own tokens. `aud` is left to the bundle:
    // the hub's public URL, which is also the resource identifier the tenant pins.
    $claims = [
        'client_id' => 'urn:lychen:%lychen.service%-api',
        'iss' => 'urn:lychen:%lychen.service%-api',
        'sub' => 'urn:lychen:%lychen.service%-api',
    ];

    // The API's tenant of the central hub (projects/common/mercure). The API publishes
    // with the tenant's publisher key; browsers subscribe with tokens signed by its
    // subscriber key instead, so a token handed to a browser can never publish. Those are
    // minted by the hub's token factory.
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

    if ($containerConfigurator->env() === 'test') {
        // Tests never reach a real hub, see MercureHubStub. It sits between the configured
        // hub and the bundle's TraceableHub decorator: a higher priority is applied first.
        $containerConfigurator->services()
            ->set(MercureHubStub::class)
            ->decorate('mercure.hub.default', priority: 1)
            ->args([service('.inner')]);
    }
};
