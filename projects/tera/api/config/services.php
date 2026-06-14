<?php

declare(strict_types=1);

use App\Security\JWT\JWTDecoder;
use App\Security\JWT\JWTEncoder;
use App\Serializer\ContextBuilder\DynamicGroupsContextBuilder;
use Aws\S3\S3Client;
use Stripe\StripeClient;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('cache_adapter', 'cache.adapter.system');

    $parameters->set('locale', 'fr');

    $parameters->set('uploads_base_url', 'https://%env(AWS_S3_BUCKET_NAME)%.s3.amazonaws.com/');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autoconfigure()
        ->autowire()
        ->bind('$zitadelProjectId', '%env(ZITADEL_PROJECT_ID)%');

    $services->load('App\\', __DIR__ . '/../src/*')
        ->exclude([
            __DIR__ . '/../src/{DependencyInjection,Entity,Migrations,Tests,Kernel.php}',
        ]);

    $services->set(JWTDecoder::class)
        ->arg('$algorithm', '%env(JWT_ALGORITHM)%')
        ->arg('$secret', '%env(JWT_SECRET)%');

    $services->set(JWTEncoder::class)
        ->arg('$algorithm', '%env(JWT_ALGORITHM)%')
        ->arg('$secret', '%env(JWT_SECRET)%');

    $services->set(DynamicGroupsContextBuilder::class)
        ->autoconfigure(false)
        ->decorate('api_platform.serializer.context_builder');

    $services->set(S3Client::class)
        ->args([
            [
                'credentials' => [
                    'key' => '%env(AWS_S3_ACCESS_ID)%',
                    'secret' => '%env(AWS_S3_ACCESS_SECRET)%',
                ],
                'region' => '%env(AWS_S3_REGION)%',
                'version' => '2006-03-01',
            ],
        ]);

    $services->set(StripeClient::class)
        ->args([
            [
                'api_key' => '%env(STRIPE_SECRET_KEY)%',
            ],
        ]);

    $services->set('common.collection_land_filter')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->arg('$properties', ['land' => null])
        ->tag('api_platform.filter');

    $services->set('common.collection_state_filter')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->arg('$properties', ['state' => null])
        ->tag('api_platform.filter');

    $services->set('land_harvest_entry.order_filter')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->arg('$orderParameterName', 'order')
        ->arg('$properties', ['createdAt' => null, 'harvestedAt' => null, 'updatedAt' => null, 'weight' => null])
        ->tag('api_platform.filter');

    $services->set('land_member_invitation.state_filter')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->arg('$properties', ['state' => null])
        ->tag('api_platform.filter');

    $services->set('land_proposal.order_filter')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->arg('$orderParameterName', 'order')
        ->arg('$properties', ['archivedAt' => null, 'createdAt' => null, 'expirationDate' => null, 'publishedAt' => null, 'updatedAt' => null])
        ->tag('api_platform.filter');

    $services->set('land_proposal.preferred_interaction_mode_filter')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->arg('$properties', ['preferredInteractionMode' => null])
        ->tag('api_platform.filter');

    $services->set('land_request.order_filter')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->arg('$orderParameterName', 'order')
        ->arg('$properties', ['archivedAt' => null, 'createdAt' => null, 'expirationDate' => null, 'publishedAt' => null, 'updatedAt' => null])
        ->tag('api_platform.filter');

    $services->set('land_request.state_filter')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->arg('$properties', ['state' => null])
        ->tag('api_platform.filter');

    $services->set('land_role.order_filter')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->arg('$orderParameterName', 'order')
        ->arg('$properties', ['position' => null])
        ->tag('api_platform.filter');

    $services->set('land_task.order_filter')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->arg('$orderParameterName', 'order')
        ->arg('$properties', ['dueDate' => null])
        ->tag('api_platform.filter');

    $services->set('land_task.state_filter')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->arg('$properties', ['state' => null])
        ->tag('api_platform.filter');
};
