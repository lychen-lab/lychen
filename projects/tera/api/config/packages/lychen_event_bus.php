<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // Events between the APIs (lychen/event-bus): tera records its events in an outbox, in
    // the transaction of the change they report, and lychen:event-bus:relay publishes them on
    // the platform's topic exchange under their type, `tera.<aggregate>.<action>.v<n>`. The
    // events it subscribes to arrive on its own queue, through the transport configured in
    // messenger.php.
    $containerConfigurator->extension('lychen_event_bus', [
        'producer' => 'tera',
        'transport' => 'async',
    ]);
};
