<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

// Firewalls, providers and the decision strategy come from lychen/symfony-config.
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
    ]);
};
