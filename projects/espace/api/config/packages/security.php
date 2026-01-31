<?php

declare(strict_types=1);

use Lychen\UtilZitadelBundle\Authenticator\ZitadelUserAuthenticator;
use Lychen\UtilZitadelBundle\UserProvider\ZitadelUserProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'access_control' => [
            [
                'path' => '^/api/docs',
                'roles' => 'PUBLIC_ACCESS',
            ],
            [
                'path' => '^/api/',
                'roles' => 'IS_AUTHENTICATED_FULLY',
            ],
        ],
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
};
