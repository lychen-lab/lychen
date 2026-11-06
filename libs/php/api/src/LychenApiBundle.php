<?php

namespace Lychen\Api;

use Lychen\Api\Doctrine\MappedAssociationResolver;
use Lychen\Api\Doctrine\UuidEntityFinder;
use Lychen\Api\Serializer\WorkflowTransitionContextBuilder;
use Lychen\Api\State\WorkflowTransitionProcessor;
use Lychen\Api\Transform\RelationToUuid;
use Lychen\Api\Transform\UuidToEntity;
use Lychen\Api\Validator\UuidExistsValidator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * Registers the services of lychen/api, whatever the API's own service configuration.
 *
 * The validator and the transforms are tagged explicitly: Symfony only finds a constraint
 * validator through `validator.constraint_validator` (otherwise it instantiates the class
 * without any argument, "Too few arguments to function ...UuidExistsValidator::__construct()"),
 * and the object mapper only finds a transform service through
 * `object_mapper.transform_callable` (otherwise '"...RelationToUuid" is not a valid
 * callable'). The workflow transition processor is an API Platform state processor
 * (`api_platform.state_processor`).
 */
final class LychenApiBundle extends AbstractBundle
{
    /**
     * @param array<string, mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $services = $container->services()
            ->defaults()
            ->autowire();

        $services->set(UuidEntityFinder::class);
        $services->set(MappedAssociationResolver::class);

        $services->set(UuidExistsValidator::class)
            ->tag('validator.constraint_validator');

        $services->set(UuidToEntity::class)
            ->tag('object_mapper.transform_callable');

        $services->set(RelationToUuid::class)
            ->tag('object_mapper.transform_callable');

        // The workflow registry only exists once the API configures a workflow.
        $services->set(WorkflowTransitionProcessor::class)
            ->args([
                service('api_platform.doctrine.orm.state.persist_processor'),
                service('workflow.registry')->nullOnInvalid(),
                service('api_platform.object_mapper'),
            ])
            ->tag('api_platform.state_processor', ['key' => WorkflowTransitionProcessor::class]);

        // Gives a transition's answer the canonical IRI of its resource, not the transition's URI.
        $services->set(WorkflowTransitionContextBuilder::class)
            ->decorate('api_platform.serializer.context_builder')
            ->args([
                service('.inner'),
                service('api_platform.metadata.resource.metadata_collection_factory'),
            ]);
    }
}
