<?php

declare(strict_types=1);

use App\Security\Authenticator\LandApiKeyAuthenticator;
use App\Security\Authenticator\PersonApiKeyAuthenticator;
use Lychen\UtilZitadelBundle\Authenticator\ZitadelUserAuthenticator;
use Lychen\UtilZitadelBundle\UserProvider\ZitadelUserProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'access_decision_manager' => [
            'strategy' => 'unanimous',
            'allow_if_all_abstain' => false,
        ],
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
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'main' => [
                'custom_authenticators' => [
                    ZitadelUserAuthenticator::class,
                    PersonApiKeyAuthenticator::class,
                    LandApiKeyAuthenticator::class,
                ],
                'lazy' => true,
                'pattern' => '^/',
                'provider' => 'all_users',
                'stateless' => true,
            ],
        ],
        'providers' => [
            'zitadel_user_provider' => [
                'id' => ZitadelUserProvider::class,
            ],
            'person_api_key' => [
                'entity' => [
                    'class' => 'App\Entity\PersonApiKey',
                    'property' => 'jti',
                ],
            ],
            'land_api_key' => [
                'entity' => [
                    'class' => 'App\Entity\LandApiKey',
                    'property' => 'jti',
                ],
            ],
            'all_users' => [
                'chain' => [
                    'providers' => [
                        'land_api_key',
                        'person_api_key',
                        'zitadel_user_provider',
                    ],
                ],
            ],
        ],
    ]);
};
