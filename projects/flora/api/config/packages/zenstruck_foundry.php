<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    if ('dev' === $containerConfigurator->env()) {
        $containerConfigurator->extension('zenstruck_foundry', [
            // Foundry 3's behaviour, which Foundry 2 asks for explicitly.
            'enable_auto_refresh_with_lazy_objects' => true,
            'faker' => [
                'locale' => 'fr_FR',
            ],
            'persistence' => [
                'flush_once' => true,
            ],
        ]);
    }
    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('zenstruck_foundry', [
            // Foundry 3's behaviour, which Foundry 2 asks for explicitly.
            'enable_auto_refresh_with_lazy_objects' => true,
            'faker' => [
                'locale' => 'fr_FR',
            ],
            'persistence' => [
                'flush_once' => true,
            ],
        ]);
    }
};
