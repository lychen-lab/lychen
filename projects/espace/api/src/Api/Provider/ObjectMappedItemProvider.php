<?php

namespace App\Api\Provider;

use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

readonly class ObjectMappedItemProvider implements ProviderInterface
{

    public function __construct(
        private ObjectMapperInterface $objectMapper,
        #[Autowire(service: ItemProvider::class)] private ProviderInterface $itemProvider
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $source = $this->itemProvider->provide($operation, $uriVariables, $context);
        return $this->objectMapper->map($source, $operation->getClass());
    }
}