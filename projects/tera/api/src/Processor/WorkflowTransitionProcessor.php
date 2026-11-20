<?php

namespace App\Processor;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Workflow\Registry;

/**
 * @implements ProcessorInterface<object, object>
 */
readonly class WorkflowTransitionProcessor implements ProcessorInterface
{
    /**
     * @param ProcessorInterface<object, object> $persistProcessor
     */
    public function __construct(#[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')] private ProcessorInterface $persistProcessor, private Registry $registry)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (!$operation instanceof HttpOperation) {
            throw new \LogicException(sprintf('Unsupported operation "%s".', get_debug_type($operation)));
        }

        $this->registry->get($data)->apply($data, $operation->getOptions()['transition']);

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
