<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('twig', [
        'date' => [
            'timezone' => 'Europe/Paris',
        ],
        'debug' => '%kernel.debug%',
        'default_path' => '%kernel.project_dir%/templates',
        'file_name_pattern' => '*.twig',
        'strict_variables' => '%kernel.debug%',
    ]);
};
