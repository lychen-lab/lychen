<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('@LychenConfig/config/packages/security.php');

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
