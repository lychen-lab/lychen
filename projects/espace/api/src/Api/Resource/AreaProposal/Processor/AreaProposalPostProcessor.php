<?php

namespace App\Api\Resource\AreaProposal\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Resource\AreaProposal\AreaProposal;
use App\Api\Resource\AreaProposal\Dto\AreaProposalPost;
use App\Repository\AreaActivityRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

/**
 * @implements ProcessorInterface<mixed, AreaProposal>
 */
final readonly class AreaProposalPostProcessor implements ProcessorInterface
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

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): AreaProposal
    {
        $entity = $this->objectMapper->map($data, \App\Entity\AreaProposal::class);

        if ($data instanceof AreaProposalPost && $data->activities) {
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
