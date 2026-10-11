<?php

namespace App\EventListener;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\State\ApiResource\Error;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Filter\LandFilter;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, method: 'validateRequest', priority: EventPriorities::PRE_READ)]
final class LandFilterValidateRequestListener
{
    public const string HAS_LAND_FILTER_ATTRIBUTE = '_has_land_filter';

    public function __construct(
        private readonly ResourceMetadataCollectionFactoryInterface $resourceMetadataFactory,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function validateRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $resourceClass = $request->attributes->get('_api_resource_class');
        $operationName = $request->attributes->get('_api_operation_name');

        // If essential attributes aren't present, it's likely not an API Platform request we care about
        if (!$resourceClass || !$operationName || Error::class === $resourceClass) {
            return;
        }

        // Get the metadata for the specific operation
        $resourceMetadataCollection = $this->resourceMetadataFactory->create($resourceClass);
        $operation = $resourceMetadataCollection->getOperation($operationName);

        if (!$operation instanceof GetCollection) {
            return;
        }

        // Get the filters defined for this operation
        $parameters = $operation->getParameters();
        if (!$parameters) {
            return;
        }
        $parameter = $operation->getParameters()->get('land');
        $operationRequiresLandFilter = false;
        if ($parameter) {
            if (LandFilter::class === $parameter->getFilter()) {
                $operationRequiresLandFilter = true;
            }
        } else {
            return;
        }

        // Add the custom attribute to the request if the filter is found
        if ($operationRequiresLandFilter) {
            $request->attributes->set(self::HAS_LAND_FILTER_ATTRIBUTE, true);
            $this->logger?->debug('LandFilter detected for operation, setting request attribute.',
                [
                    'resource_class' => $resourceClass,
                    'operation_name' => $operationName,
                    'attribute' => self::HAS_LAND_FILTER_ATTRIBUTE,
                ]);
            $landUlid = $request->query->get('land');
            if (empty($landUlid)) {
                throw new HttpException(400, "Land query parameter shouldn't be empty");
            }
        }
    }
}
