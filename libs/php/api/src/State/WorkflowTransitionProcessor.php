<?php

namespace Lychen\Api\State;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\TransitionBlocker;

/**
 * Applies a workflow transition to the entity behind an API resource kept apart from the
 * Doctrine model, persists it, and answers with the resource mapped from it.
 *
 *     #[Patch(
 *         uriTemplate: '/seed_lots/{uuid}/reserve',
 *         options: ['transition' => 'reserve'],
 *         input: false,
 *         map: false,
 *         processor: WorkflowTransitionProcessor::class,
 *     )]
 *
 * - map: false: the provider then hands over the entity itself, the subject of the
 *   workflow. With the mapping on, $data would be the resource, and the object mapper would
 *   first map that resource back onto the entity.
 * - input: false: a transition takes no body (no deserialization, no validation of one).
 * - options: `transition` names the transition; `workflow` names the workflow, for an entity
 *   that supports several.
 *
 * A transition the current state does not allow is a 409 Conflict, carrying the reasons the
 * workflow gives (its transition blockers).
 *
 * @implements ProcessorInterface<object, object>
 */
final readonly class WorkflowTransitionProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<object, object> $persistProcessor
     */
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private ?Registry $workflows,
        private ObjectMapperInterface $objectMapper,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object
    {
        if (null === $this->workflows) {
            throw new \LogicException(\sprintf('%s needs Symfony workflows: configure at least one under framework.workflows.', self::class));
        }

        $options = $operation instanceof HttpOperation ? $operation->getOptions() ?? [] : [];
        $transition = $options['transition'] ?? null;
        if (!\is_string($transition)) {
            throw new \LogicException(\sprintf('The "%s" operation must name its transition: options: [\'transition\' => \'...\'].', $operation->getName()));
        }

        if (!\is_object($data) || $data::class === $operation->getClass()) {
            throw new \LogicException(\sprintf('The "%s" operation must hand the entity over to %s: set map: false on it.', $operation->getName(), self::class));
        }

        $workflowName = $options['workflow'] ?? null;
        $workflow = $this->workflows->get($data, \is_string($workflowName) ? $workflowName : null);

        if (!$workflow->can($data, $transition)) {
            $reasons = array_map(
                static fn (TransitionBlocker $blocker): string => $blocker->getMessage(),
                iterator_to_array($workflow->buildTransitionBlockerList($data, $transition)),
            );

            throw new ConflictHttpException(trim(\sprintf('The "%s" transition cannot be applied. %s', $transition, implode(' ', $reasons))));
        }

        $workflow->apply($data, $transition);

        $persisted = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        return $this->objectMapper->map(\is_object($persisted) ? $persisted : $data, $operation->getClass());
    }
}
