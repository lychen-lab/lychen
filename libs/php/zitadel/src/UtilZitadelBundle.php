<?php

namespace Lychen\UtilZitadelBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class UtilZitadelBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('./Config/services.yml');

        $container->services()->get('Lychen\UtilZitadelBundle\UserProvider\ZitadelUserProvider')->arg('$userClass', $config['user']['class']);
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('./Config/definition.php');
    }
}
