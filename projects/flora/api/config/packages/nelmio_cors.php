<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('nelmio_cors', [
        'defaults' => [
            'allow-credentials' => true,
            'allow_headers' => [
                'Content-Type',
                'Authorization',
                'Preload',
                'Fields',
            ],
            'allow_methods' => [
                'GET',
                'OPTIONS',
                'POST',
                'PUT',
                'PATCH',
                'DELETE',
                'HEAD',
            ],
            'allow_origin' => [
                '%env(CORS_ALLOW_ORIGIN)%',
            ],
            'expose_headers' => [
                'Link',
                'Location',
                'Access-granted',
                'Customer-session-token',
            ],
            'max_age' => 3600,
            'origin_regex' => true,
        ],
        'paths' => [
            '^/' => null,
        ],
    ]);
};
