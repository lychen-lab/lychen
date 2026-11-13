<?php

namespace App\Api\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\AreaListingInterface;
use App\Workflow\AreaLifecycle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * Persists the content PATCH of a listing, which only a draft accepts: once submitted,
 * what moderation reads — and then publishes — must stay what it validated. Like a
 * transition the place does not allow, it is a 409 Conflict.
 *
 * It runs where the Doctrine persist processor would, after API Platform has mapped the
 * Patch DTO onto the entity: refusing here leaves those changes unflushed.
 *
 * @implements ProcessorInterface<object, object>
 */
final readonly class DraftContentProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<object, object> $persistProcessor
     */
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private AreaLifecycle $lifecycle,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        if (!$data instanceof AreaListingInterface) {
            throw new \LogicException(\sprintf('The "%s" operation must hand an area listing entity over to %s.', $operation->getName(), self::class));
        }

        if (!$this->lifecycle->isEditable($data)) {
            throw new ConflictHttpException(\sprintf('Only a draft can be edited: this one is "%s".', $data->getPlace()));
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
