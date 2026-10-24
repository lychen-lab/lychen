<?php

declare(strict_types=1);

use Lychen\UtilZitadelBundle\Authenticator\ZitadelUserAuthenticator;
use Lychen\UtilZitadelBundle\UserProvider\ZitadelUserProvider;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

// No access_control here: SecurityBundle only accepts it from a single config, so each
// API declares its own rules. No firewall can be added after these either: an API adds
// its authenticators to `main` and its providers to `all_users`, which are lists and get
// appended after the Zitadel ones.
return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
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
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->services()
            ->alias('security', Security::class)
            ->public();
    }
};
