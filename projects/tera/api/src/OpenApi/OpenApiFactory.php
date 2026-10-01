<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsDecorator(decorates: 'api_platform.openapi.factory')]
readonly class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(
        #[Autowire('@App\OpenApi\OpenApiFactory.inner')]
        private OpenApiFactoryInterface $decorated,
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        return $this->decorated->__invoke($context);
    }
}
