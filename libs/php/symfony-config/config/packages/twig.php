<?php

declare(strict_types=1);

return [
    'twig' => [
        'date' => [
            'timezone' => 'Europe/Paris',
        ],
        'debug' => '%kernel.debug%',
        'default_path' => '%kernel.project_dir%/templates',
        'file_name_pattern' => '*.twig',
        'strict_variables' => '%kernel.debug%',
    ],
    'when@test' => [
        'twig' => [
            'strict_variables' => true,
        ],
    ],
];
