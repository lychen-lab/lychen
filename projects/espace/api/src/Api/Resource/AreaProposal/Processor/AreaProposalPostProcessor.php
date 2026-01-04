<?php

namespace App\Api\Resource\AreaProposal\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Resource\AreaProposal\AreaProposal;
use App\Api\Resource\AreaProposal\Dto\AreaProposalPost;
use App\Entity\AreaProposal as AreaProposalEntity;
use App\Repository\AreaActivityRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

final readonly class AreaProposalPostProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface     $persistProcessor,
        private ObjectMapperInterface  $objectMapper,
        private AreaActivityRepository $areaActivityRepository,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): AreaProposal
    {
        dump($data);
        $entity = new AreaProposalEntity();
        dump($entity);
        $entity = $this->objectMapper->map($data, $entity);
        dump($entity);
        if ($data instanceof AreaProposalPost && $data->activities) {
            foreach ($data->activities as $activityCode) {
                if ($activity = $this->areaActivityRepository->findOneBy(['code' => $activityCode])) {
                    dump($activity);
                    $entity->addActivity($activity);
                }
            }
        }
        dump($entity);
        $entity = $this->persistProcessor->process($entity, $operation, $uriVariables, $context);

        dump($entity);

        return $this->objectMapper->map($entity, $operation->getClass());
    }
}
