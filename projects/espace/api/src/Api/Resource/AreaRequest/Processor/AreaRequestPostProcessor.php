<?php

namespace App\Api\Resource\AreaRequest\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Resource\AreaRequest\AreaRequest;
use App\Api\Resource\AreaRequest\Dto\AreaRequestPost;
use App\Repository\AreaActivityRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

/**
 * @implements ProcessorInterface<mixed, AreaRequest>
 */
final readonly class AreaRequestPostProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<mixed, mixed> $persistProcessor
     */
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private ObjectMapperInterface $objectMapper,
        private AreaActivityRepository $areaActivityRepository,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): AreaRequest
    {
        $entity = $this->objectMapper->map($data, \App\Entity\AreaRequest::class);

        if ($data instanceof AreaRequestPost && $data->activities) {
            foreach ($data->activities as $activityCode) {
                if ($activity = $this->areaActivityRepository->findOneBy(['code' => $activityCode])) {
                    $entity->addActivity($activity);
                }
            }
        }

        $entity = $this->persistProcessor->process($entity, $operation, $uriVariables, $context);

        return $this->objectMapper->map($entity, $operation->getClass());
    }
}
