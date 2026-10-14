<?php

namespace App\Api\Workflow;

/**
 * The workflow a resource goes through, as a Mermaid flowchart a front renders with
 * mermaid.js: the diagram the profiler's Workflow panel draws, with the places the
 * resource is currently in outlined.
 */
final class WorkflowDiagram
{
    public function __construct(
        /** Name of the Symfony workflow, e.g. `area_request_validation`. */
        public string $name,
        /** Mermaid flowchart source: places are nodes, the initial one rounded, the current ones with a 4px border. */
        public string $mermaid,
    ) {
    }
}
