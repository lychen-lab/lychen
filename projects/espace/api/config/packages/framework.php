<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'disallow_search_engine_index' => true,
        'handle_all_throwables' => true,
        'http_method_override' => false,
        'php_errors' => [
            'log' => true,
        ],
        'secret' => '%env(APP_SECRET)%',
        'trusted_proxies' => '127.0.0.1,REMOTE_ADDR',
        'validation' => [
            'email_validation_mode' => 'strict',
        ],
    ]);
    if ($containerConfigurator->env() === 'test') {
        $containerConfigurator->extension('framework', [
            'test' => true,
        ]);
    }
};
