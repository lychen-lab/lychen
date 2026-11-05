<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    if ('dev' === $containerConfigurator->env()) {
        $containerConfigurator->extension('framework', [
            'profiler' => [
                'collect_serializer_data' => true,
                'only_exceptions' => false,
            ],
        ]);
        $containerConfigurator->extension('web_profiler', [
            'intercept_redirects' => false,
            'toolbar' => true,
        ]);
    }
    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('framework', [
            'profiler' => [
                'collect' => false,
            ],
        ]);
        $containerConfigurator->extension('web_profiler', [
            'intercept_redirects' => false,
            'toolbar' => false,
        ]);
    }
};
