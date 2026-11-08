<?php

namespace Lychen\Api\Serializer;

use ApiPlatform\Metadata\Exception\OperationNotFoundException;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\State\SerializerContextBuilderInterface;
use Lychen\Api\State\WorkflowTransitionProcessor;
use Symfony\Component\HttpFoundation\Request;

/**
 * Gives the answer of a workflow transition the canonical IRI of its resource.
 *
 * API Platform builds the @id of an item from the operation answering the request: for
 * PATCH /seed_lots/{uuid}/reserve, "/api/seed_lots/{uuid}/reserve", which identifies no
 * resource. A Patch cannot declare an itemUriTemplate, so for the operations handled by
 * WorkflowTransitionProcessor, this decorator points the normalization's
 * item_uri_template at the resource's item GET, unless the operation's normalization
 * context already names one.
 */
final readonly class WorkflowTransitionContextBuilder implements SerializerContextBuilderInterface
{
    public function __construct(
        private SerializerContextBuilderInterface $decorated,
        private ResourceMetadataCollectionFactoryInterface $resourceMetadataCollectionFactory,
    ) {
    }

    public function createFromRequest(Request $request, bool $normalization, ?array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request, $normalization, $extractedAttributes);

        $operation = $context['operation'] ?? null;
        if (
            !$normalization
            || isset($context['item_uri_template'])
            || !$operation instanceof HttpOperation
            || WorkflowTransitionProcessor::class !== $operation->getProcessor()
            || null === ($class = $operation->getClass())
        ) {
            return $context;
        }

        try {
            $context['item_uri_template'] = $this->resourceMetadataCollectionFactory->create($class)->getOperation(null, false, true)->getName();
        } catch (OperationNotFoundException) {
            // No item GET to name the resource by: keep API Platform's IRI.
        }

        return $context;
    }
}
