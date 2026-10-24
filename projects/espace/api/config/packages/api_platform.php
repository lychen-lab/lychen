<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import('@LychenConfig/config/packages/api_platform.php');

    $containerConfigurator->extension('api_platform', [
        'title' => 'Espace',
    ]);
};
