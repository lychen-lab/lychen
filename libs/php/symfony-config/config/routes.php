<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

// Imported by each API's config/routes.php, before its own controllers.
return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import('.', 'api_platform')
        ->prefix('/api');

    $routingConfigurator->import('security.route_loader.logout', 'service');

    if ('dev' === $routingConfigurator->env()) {
        $routingConfigurator->import('@FrameworkBundle/Resources/config/routing/errors.php')
            ->prefix('/_error');
        $routingConfigurator->import('@WebProfilerBundle/Resources/config/routing/wdt.php')
            ->prefix('/_wdt');
        $routingConfigurator->import('@WebProfilerBundle/Resources/config/routing/profiler.php')
            ->prefix('/_profiler');
    }
};
