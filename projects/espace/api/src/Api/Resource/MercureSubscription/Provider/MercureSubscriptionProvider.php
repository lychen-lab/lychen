<?php

namespace App\Api\Resource\MercureSubscription\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Resource\MercureSubscription\MercureSubscription;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\Grant;
use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;

/**
 * @implements ProviderInterface<MercureSubscription>
 */
final readonly class MercureSubscriptionProvider implements ProviderInterface
{
    /**
     * Topic selectors any signed-in user may subscribe to: the same audience as the
     * matching GET operations. Add a resource's IRI template here when it starts publishing.
     */
    public const TOPICS = [
        '/api/area_proposals/{uuid}',
    ];

    public function __construct(
        private HubInterface $hub,
        #[Autowire(service: 'app.mercure.subscriber_token_factory')]
        private TokenFactoryInterface $subscriberTokenFactory,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): MercureSubscription
    {
        return new MercureSubscription(
            hubUrl: $this->hub->getPublicUrl(),
            token: $this->subscriberTokenFactory->create([
                new Grant([Grant::ACTION_SUBSCRIBE], self::TOPICS),
            ]),
        );
    }
}
