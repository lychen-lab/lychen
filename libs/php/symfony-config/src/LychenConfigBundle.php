<?php

declare(strict_types=1);

namespace Lychen\ConfigBundle;

use Lychen\ConfigBundle\DependencyInjection\Compiler\StubMercureHubPass;
use Monolog\Logger;
use Sentry\Monolog\Handler;
use Sentry\State\HubInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * The Symfony configuration every Lychen API shares, so the backend services stay aligned.
 *
 * The presets in config/packages are *prepended*: the application's own config/packages
 * files are merged on top of them and always have the last word. An application only
 * declares what is its own — its name, its access rules, its extra authenticators — or
 * overrides a preset key in a file of its own.
 *
 * Presets must return arrays. A file returning a closure that calls
 * `$containerConfigurator->extension()` would be appended instead, and silently override
 * the application.
 */
final class LychenConfigBundle extends AbstractBundle
{
    /**
     * Preset => the extensions it configures. A preset is only imported when all of them
     * are registered for the current environment: web_profiler, debug, foundry and dama
     * only exist in dev/test, sentry only in prod, mercure only in the APIs that publish.
     *
     * The list follows the alphabetical order the kernel loads config/packages in.
     */
    private const array PRESETS = [
        'api_platform' => ['api_platform'],
        'dama_doctrine_test' => ['dama_doctrine_test'],
        'debug' => ['debug'],
        'doctrine' => ['doctrine'],
        'doctrine_migrations' => ['doctrine_migrations'],
        'framework' => ['framework'],
        'mercure' => ['mercure'],
        'monolog' => ['monolog'],
        'nelmio_cors' => ['nelmio_cors'],
        'security' => ['security', 'util_zitadel'],
        'sentry' => ['sentry', 'monolog'],
        'snc_redis' => ['snc_redis'],
        'twig' => ['twig'],
        'web_profiler' => ['web_profiler'],
        'zenstruck_foundry' => ['zenstruck_foundry'],
    ];

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('service')
                    ->info('The API\'s domain (tera, espace, flora…): its RabbitMQ queue and routing keys, its OpenAPI title.')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
            ->end();
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // The bundle's own config is only processed after every prepend, so read it raw.
        $service = array_merge(...$builder->getExtensionConfig('lychen_config'))['service'] ?? null;
        if (!\is_string($service) || !preg_match('/^[a-z][a-z0-9_]*$/', $service)) {
            throw new InvalidArgumentException('"lychen_config.service" must be the API\'s domain in lowercase, such as "espace".');
        }

        $builder->setParameter('lychen.service', $service);
        $builder->setParameter('lychen.api_title', ucfirst($service));

        // Every import is prepended in front of the previous ones: walk the list backwards
        // so the presets end up merged in the order they are listed.
        foreach (array_reverse(self::PRESETS) as $preset => $extensions) {
            foreach ($extensions as $extension) {
                if (!$builder->hasExtension($extension)) {
                    continue 2;
                }
            }

            $container->import("../config/packages/{$preset}.php");
        }
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $services = $container->services();
        $bundles = $builder->getParameter('kernel.bundles');

        // monolog-bundle 4 removed the built-in "sentry" handler type: the handler is a
        // service, wired through a "service" handler by the sentry preset.
        // https://docs.sentry.io/platforms/php/guides/symfony/integrations/monolog/
        if (isset($bundles['SentryBundle'])) {
            $services->set(Handler::class)
                ->arg('$hub', service(HubInterface::class))
                ->arg('$level', Logger::ERROR)
                ->arg('$fillExtraContext', true);
        }

        if ('test' === $container->env()) {
            $services->alias('security', Security::class)->public();
        }
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new StubMercureHubPass());
    }
}
