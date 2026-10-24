<?php

declare(strict_types=1);

// The hub (Mercure 1.0) takes OAuth 2.0 access tokens (RFC 9068), which name their
// issuer, subject and client. `iss` has to be the issuer the API's tenant of the hub
// trusts, `urn:lychen:<service>-api` (see projects/common/mercure/entrypoint.sh); the API
// is the client, and the subject of its own tokens. `aud` is left to the bundle: the
// hub's public URL, which is also the resource identifier the tenant pins.
$claims = [
    'client_id' => 'urn:lychen:%lychen.service%-api',
    'iss' => 'urn:lychen:%lychen.service%-api',
    'sub' => 'urn:lychen:%lychen.service%-api',
];

// The API's tenant of the central hub (projects/common/mercure). The API publishes with
// the tenant's publisher key; browsers subscribe with tokens signed by its subscriber key
// instead, so a token handed to a browser can never publish. Those are minted by the
// hub's token factory, see espace's MercureSubscriptionProvider.
return [
    'mercure' => [
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
    ],
];
