<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('@LychenConfig/config/packages/nelmio_cors.php');

    // Appended to the shared methods.
    $containerConfigurator->extension('nelmio_cors', [
        'defaults' => [
            'allow_methods' => [
                'HEAD',
            ],
        ],
    ]);
};
