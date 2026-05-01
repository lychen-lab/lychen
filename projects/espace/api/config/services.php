<?php

declare(strict_types=1);

use App\Temporal\Activity\ValidationAreaProposalActivities;
use App\Temporal\TemporalClientFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Temporal\Client\WorkflowClientInterface;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('cache_adapter', 'cache.adapter.system');

    $parameters->set('locale', 'fr');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autoconfigure()
        ->autowire();

    $services->load('App\\', __DIR__ . '/../src/*')
        ->exclude([
            __DIR__ . '/../src/{DependencyInjection,Entity,Migrations,Tests,Kernel.php}',
        ]);

    $services->set(WorkflowClientInterface::class)
        ->factory([TemporalClientFactory::class, 'create'])
        ->args([env('TEMPORAL_ADDRESS')])
        ->public();

    $services->set(ValidationAreaProposalActivities::class)
        ->autowire()
        ->arg('$novuApiUrl', env('NOVU_API_URL'))
        ->arg('$novuApiKey', env('NOVU_API_KEY'))
        ->public();
};
