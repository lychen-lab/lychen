<?php

namespace App\Api\Workflow;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Workflow\Debug\TraceableWorkflow;
use Symfony\Component\Workflow\Dumper\MermaidDumper;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\StateMachine;

/**
 * Serves the `/workflow` operation of a resource: loads its entity like the resource's
 * own Get does, so AreaVisibilityExtension applies and what the user may not read
 * answers 404, then dumps the entity's workflow with Symfony's MermaidDumper — the one
 * behind the profiler's Workflow panel and `workflow:dump` — passing its marking so
 * the current places stand out.
 *
 * The operation must set `map: false`: the diagram is not a mapped representation of
 * the entity.
 *
 * @implements ProviderInterface<WorkflowDiagram>
 */
final readonly class WorkflowDiagramProvider implements ProviderInterface
{
    /**
     * @param ProviderInterface<object> $itemProvider
     */
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private ProviderInterface $itemProvider,
        private Registry $workflows,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?WorkflowDiagram
    {
        $subject = $this->itemProvider->provide($operation, $uriVariables, $context);
        if (!\is_object($subject)) {
            return null;
        }

        $workflow = $this->workflows->get($subject);

        // Same choice as `workflow:dump`: a state machine labels its arrows with the
        // transitions, a workflow draws them as nodes. In debug, the registry hands out
        // the profiler's TraceableWorkflow, which hides the actual type.
        $definedWorkflow = $workflow instanceof TraceableWorkflow ? $workflow->getInner() : $workflow;
        $dumper = new MermaidDumper($definedWorkflow instanceof StateMachine
            ? MermaidDumper::TRANSITION_TYPE_STATEMACHINE
            : MermaidDumper::TRANSITION_TYPE_WORKFLOW);

        return new WorkflowDiagram(
            name: $workflow->getName(),
            mermaid: $dumper->dump($workflow->getDefinition(), $workflow->getMarking($subject)),
        );
    }
}
