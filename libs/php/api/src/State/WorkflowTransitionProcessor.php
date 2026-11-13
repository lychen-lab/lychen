<?php

namespace Lychen\Api\State;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
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
 *   A transition that needs data names an input DTO instead (`input: SeedLotGive::class`),
 *   marked #[Map(target: Entity::class)] like a Post or Patch DTO: API Platform validates
 *   it, and it is mapped onto the entity before the workflow is asked, so that the
 *   workflow's guards see what was sent.
 * - options: `transition` names the transition; `workflow` names the workflow, for an entity
 *   that supports several.
 *
 * A transition the current state does not allow is a 409 Conflict, carrying the reasons the
 * workflow gives (its transition blockers). Nothing is persisted then, the input included.
 *
 * The transition runs in one transaction, on the entity locked and read again under that lock:
 *
 *     BEGIN
 *     SELECT ... FOR UPDATE      -- a concurrent transition on the same entity waits here
 *     map the input              -- onto the entity read again, if the transition takes one
 *     can(), apply()             -- guards and listeners see the committed state and the input
 *     flush                      -- the entity, and what the listeners persisted
 *     COMMIT
 *
 * so two concurrent requests cannot both pass the guard (the second sees the first's outcome
 * and gets its 409), and whatever the workflow's listeners persist, such as an event recorded
 * in lychen/event-bus's outbox, commits with the new state or not at all.
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
        private ManagerRegistry $doctrine,
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

        // With an input DTO, $data is that DTO, and the entity is what the provider read.
        $input = null;
        $inputClass = $operation->getInput()['class'] ?? null;
        if (\is_string($inputClass) && $data instanceof $inputClass) {
            $input = $data;
            $data = $context['read_data'] ?? null;
        }

        if (!\is_object($data) || $data::class === $operation->getClass()) {
            throw new \LogicException(\sprintf('The "%s" operation must hand the entity over to %s: set map: false on it.', $operation->getName(), self::class));
        }

        $manager = $this->doctrine->getManagerForClass($data::class);
        if (!$manager instanceof EntityManagerInterface) {
            throw new \LogicException(\sprintf('%s needs a Doctrine entity: no entity manager maps %s.', self::class, $data::class));
        }

        $workflowName = $options['workflow'] ?? null;
        $workflow = $this->workflows->get($data, \is_string($workflowName) ? $workflowName : null);

        // Not EntityManager::wrapInTransaction(): it closes the entity manager on any exception,
        // a 409 included, and the rest of the request (or the next one, in worker mode) needs it.
        $connection = $manager->getConnection();
        $connection->beginTransaction();
        try {
            // lock() locks the entity's own row only (SELECT 1 ... FOR UPDATE), where
            // refresh($data, PESSIMISTIC_WRITE) would lock the query that also joins its eager
            // relations, which PostgreSQL refuses on the nullable side of a LEFT JOIN.
            $manager->lock($data, LockMode::PESSIMISTIC_WRITE);
            $manager->refresh($data);

            // After the refresh, which would otherwise discard it: the guards see what was sent.
            if (null !== $input) {
                $this->objectMapper->map($input, $data);
            }

            if (!$workflow->can($data, $transition)) {
                $reasons = array_map(
                    static fn (TransitionBlocker $blocker): string => $blocker->getMessage(),
                    iterator_to_array($workflow->buildTransitionBlockerList($data, $transition)),
                );

                throw new ConflictHttpException(trim(\sprintf('The "%s" transition cannot be applied. %s', $transition, implode(' ', $reasons))));
            }

            $workflow->apply($data, $transition);

            $persisted = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

            $connection->commit();
        } catch (\Throwable $exception) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }

            throw $exception;
        }

        return $this->objectMapper->map(\is_object($persisted) ? $persisted : $data, $operation->getClass());
    }
}
