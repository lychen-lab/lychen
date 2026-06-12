<?php

namespace App\Api\Resource\AreaProposal\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Api\Resource\AreaProposal\AreaProposal;
use App\Api\Resource\AreaProposal\Dto\AreaProposalPatch;
use App\Repository\AreaActivityRepository;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;

/**
 * @implements ProcessorInterface<mixed, AreaProposal>
 */
final readonly class AreaProposalPatchProcessor implements ProcessorInterface
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
        if (!$entity = $context['request']->attributes->get('read_data')) {
            throw new NotFoundHttpException('Not Found');
        }
        \assert($entity instanceof \App\Entity\AreaProposal);

        // Map standard fields
        $entity = $this->objectMapper->map($data, $entity);

        // Handle activities relation
        if ($data instanceof AreaProposalPatch && null !== $data->activities) {
            // Get current activities associated with the entity
            $currentActivities = $entity->getActivities();

            // Find all new activities requested by code
            $newActivities = [];
            foreach ($data->activities as $activityCode) {
                if ($activity = $this->areaActivityRepository->findOneBy(['code' => $activityCode])) {
                    $newActivities[] = $activity;
                }
            }

            // Remove activities that are not in the new list
            foreach ($currentActivities as $currentActivity) {
                if (!in_array($currentActivity, $newActivities, true)) {
                    $entity->removeActivity($currentActivity);
                }
            }

            // Add activities that are in the new list but not yet in the entity
            foreach ($newActivities as $newActivity) {
                if (!$currentActivities->contains($newActivity)) {
                    $entity->addActivity($newActivity);
                }
            }
        }

        $entity = $this->persistProcessor->process($entity, $operation, $uriVariables, $context);

        return $this->objectMapper->map($entity, $operation->getClass());
    }
}
