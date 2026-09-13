<?php

namespace App\Mercure;

use ApiPlatform\Metadata\IriConverterInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\UrlGeneratorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mercure\Exception\RuntimeException;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Publishes API resources to the Mercure hub.
 *
 * API Platform's own Mercure integration cannot do it here: its Doctrine listener only
 * publishes entities that are resources themselves, and this API's resources are DTOs
 * mapped from the entities (`stateOptions` + ObjectMapper).
 *
 * Updates are always private: the hub only delivers them to subscribers whose token
 * allows their topic, see MercureSubscriptionProvider. That topic is the IRI *path*
 * (`/api/area_proposals/…`) rather than an absolute URL, so that a change made from the
 * CLI or a worker — where no request tells the router which host it serves — lands on
 * the same topic as one made through the API.
 */
final readonly class ResourceUpdatePublisher
{
    public function __construct(
        private HubInterface $hub,
        private IriConverterInterface $iriConverter,
        private ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
        private SerializerInterface $serializer,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Publishes a created or updated resource, serialized as `GET` on its IRI returns it.
     */
    public function publish(object $resource): void
    {
        // The app uses this payload in place of a GET, so serialize it the way API Platform
        // serializes one: with the item operation's context, plus the default its request
        // context builder adds on top, which an operation can still override.
        $operation = $this->resourceMetadataCollectionFactory->create($resource::class)->getOperation();
        $context = ($operation->getNormalizationContext() ?? []) + [
            'operation' => $operation,
            'resource_class' => $resource::class,
        ];
        $context['skip_null_values'] ??= true;

        $iri = $this->iriConverter->getIriFromResource($resource, UrlGeneratorInterface::ABS_PATH, $operation);

        $this->send(new Update($iri, $this->serializer->serialize($resource, 'jsonld', $context), private: true));
    }

    /**
     * Publishes the deletion of a resource which, by API Platform's convention, carries
     * nothing but its `@id`.
     *
     * @param class-string         $resourceClass
     * @param array<string, mixed> $uriVariables
     */
    public function publishDeletion(string $resourceClass, array $uriVariables): void
    {
        $iri = $this->iriConverter->getIriFromResource(
            $resourceClass,
            UrlGeneratorInterface::ABS_PATH,
            context: ['uri_variables' => $uriVariables],
        );

        $this->send(new Update($iri, json_encode(['@id' => $iri], \JSON_THROW_ON_ERROR), private: true));
    }

    private function send(Update $update): void
    {
        try {
            $this->hub->publish($update);
        } catch (RuntimeException $exception) {
            // Real-time is a convenience layered over the API, not part of its contract: the
            // write this update describes is already committed, so an unreachable hub must
            // not turn it into an error. Subscribers catch up on their next fetch.
            $this->logger->warning('Could not publish a Mercure update: {message}', [
                'message' => $exception->getMessage(),
                'topics' => $update->getTopics(),
                'exception' => $exception,
            ]);
        }
    }
}
