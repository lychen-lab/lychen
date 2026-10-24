<?php

namespace App\Api\Resource\MercureSubscription\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Resource\MercureSubscription\MercureSubscription;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\Grant;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @implements ProviderInterface<MercureSubscription>
 */
final readonly class MercureSubscriptionProvider implements ProviderInterface
{
    /**
     * Topics any signed-in user may subscribe to, by matcher type: the same audience as the
     * matching GET operations. Add a resource's IRI here, as a URL pattern
     * (`/api/things/:uuid`), when it starts publishing.
     */
    public const TOPICS = [
        'urlpattern' => [
            '/api/area_proposals/:uuid',
        ],
    ];

    private const TOKEN_LIFETIME = '+1 hour';

    public function __construct(
        private HubInterface $hub,
        private Security $security,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): MercureSubscription
    {
        $user = $this->security->getUser() ?? throw new AccessDeniedException('Only a signed-in user may subscribe to the hub.');

        // The hub's token factory is the one configured with the tenant's *subscriber* key,
        // and adds the claims the hub requires (config/packages/mercure.php).
        $tokenFactory = $this->hub->getFactory() ?? throw new \LogicException('The Mercure hub has no subscriber token factory.');

        return new MercureSubscription(
            hubUrl: $this->hub->getPublicUrl(),
            token: $tokenFactory->create([new Grant([Grant::ACTION_SUBSCRIBE], self::TOPICS)], [
                'exp' => new \DateTimeImmutable(self::TOKEN_LIFETIME),
                // RFC 9068: a token issued on behalf of a user names that user as its subject.
                'sub' => $user->getUserIdentifier(),
            ]),
        );
    }
}
