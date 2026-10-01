<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $routes->import('@LychenConfigBundle/config/routes.php');
    $routes->import('../src/Controller/', 'attribute');
};
