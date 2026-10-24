<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('monolog', [
        'channels' => [
            'deprecation',
            'command',
            'worker',
        ],
        'handlers' => [
            'command' => [
                'channels' => [
                    'command',
                ],
                'level' => 'debug',
                'path' => '%kernel.logs_dir%/%kernel.environment%_commands.log',
                'type' => 'stream',
            ],
            'worker' => [
                'channels' => [
                    'worker',
                ],
                'level' => 'debug',
                'path' => '%kernel.logs_dir%/%kernel.environment%_workers.log',
                'type' => 'stream',
            ],
        ],
    ]);
    if ($containerConfigurator->env() === 'dev') {
        $containerConfigurator->extension('monolog', [
            'handlers' => [
                'console' => [
                    'channels' => [
                        '!event',
                        '!doctrine',
                        '!console',
                        '!command',
                        '!worker',
                    ],
                    'process_psr_3_messages' => false,
                    'type' => 'console',
                ],
                'main' => [
                    'channels' => [
                        '!event',
                        '!command',
                        '!worker',
                    ],
                    'level' => 'debug',
                    'path' => '%kernel.logs_dir%/%kernel.environment%.log',
                    'type' => 'stream',
                ],
            ],
        ]);
    }
    if ($containerConfigurator->env() === 'prod') {
        $containerConfigurator->extension('monolog', [
            'handlers' => [
                'command' => [
                    'channels' => [
                        'command',
                    ],
                    'level' => 'debug',
                    'max_files' => 30,
                    'path' => '%kernel.logs_dir%/%kernel.environment%_command.log',
                    'type' => 'rotating_file',
                ],
                'main' => [
                    'action_level' => 'error',
                    'buffer_size' => 50,
                    'excluded_http_codes' => [
                        404,
                        405,
                    ],
                    'handler' => 'main_nested',
                    'type' => 'fingers_crossed',
                ],
                'main_nested' => [
                    'channels' => [
                        '!event',
                    ],
                    'level' => 'debug',
                    'max_files' => 30,
                    'path' => '%kernel.logs_dir%/%kernel.environment%.log',
                    'type' => 'rotating_file',
                ],
                'worker' => [
                    'action_level' => 'error',
                    'buffer_size' => 50,
                    'excluded_http_codes' => [
                        404,
                        405,
                    ],
                    'handler' => 'worker_nested',
                    'type' => 'fingers_crossed',
                ],
                'worker_nested' => [
                    'channels' => [
                        'worker',
                    ],
                    'level' => 'debug',
                    'max_files' => 30,
                    'path' => '%kernel.logs_dir%/%kernel.environment%_worker.log',
                    'type' => 'rotating_file',
                ],
            ],
        ]);
    }
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('monolog', [
            'handlers' => [
                'main' => [
                    'action_level' => 'error',
                    'channels' => [
                        '!event',
                        '!command',
                        '!worker',
                    ],
                    'excluded_http_codes' => [
                        404,
                        405,
                    ],
                    'handler' => 'nested',
                    'type' => 'fingers_crossed',
                ],
                'nested' => [
                    'level' => 'debug',
                    'path' => '%kernel.logs_dir%/%kernel.environment%.log',
                    'type' => 'stream',
                ],
            ],
        ]);
    }
};
