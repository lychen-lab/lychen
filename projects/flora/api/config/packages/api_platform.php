<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('api_platform', [
        'defaults' => [
            'extra_properties' => [
                'standard_put' => true,
            ],
            'pagination_client_enabled' => true,
            'pagination_client_items_per_page' => true,
        ],
        'description' => ' ',
        'error_formats' => [
            'jsonld' => [
                'mime_types' => [
                    'application/ld+json',
                ],
            ],
        ],
        'formats' => [
            'jsonld' => [
                'application/ld+json',
            ],
        ],
        'mapping' => [
            'paths' => [
                '%kernel.project_dir%/src/Entity',
            ],
        ],
        'patch_formats' => [
            'json' => [
                'application/merge-patch+json',
            ],
        ],
        'serializer' => [
            'hydra_prefix' => false,
        ],
        'show_webby' => false,
        'swagger' => [
            'api_keys' => [
                'apiKey' => [
                    'name' => 'Authorization',
                    'type' => 'header',
                ],
            ],
            'versions' => [
                3,
            ],
        ],
        'title' => 'Flora',
        'use_symfony_listeners' => true,
    ]);
};
