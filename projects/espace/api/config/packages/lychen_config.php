<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

// The shared configuration of the Lychen APIs (libs/php/symfony-config). Every other
// file in config/packages is merged on top of it.
return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('lychen_config', [
        'service' => 'espace',
    ]);
};
