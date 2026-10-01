<?php

declare(strict_types=1);

use Lychen\UtilZitadelBundle\Authenticator\ZitadelUserAuthenticator;
use Lychen\UtilZitadelBundle\UserProvider\ZitadelUserProvider;

// No access_control here: SecurityBundle only accepts it from a single config, so each
// API declares its own rules. The firewalls cannot be extended with new keys either —
// an API adds its own authenticators to `main` and its own providers to `all_users`,
// which are lists and get appended after the Zitadel ones.
return [
    'security' => [
        'access_decision_manager' => [
            'allow_if_all_abstain' => false,
            'strategy' => 'unanimous',
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'main' => [
                'custom_authenticators' => [
                    ZitadelUserAuthenticator::class,
                ],
                'lazy' => true,
                'pattern' => '^/',
                'provider' => 'all_users',
                'stateless' => true,
            ],
        ],
        'providers' => [
            'all_users' => [
                'chain' => [
                    'providers' => [
                        'zitadel_user_provider',
                    ],
                ],
            ],
            'zitadel_user_provider' => [
                'id' => ZitadelUserProvider::class,
            ],
        ],
    ],
];
