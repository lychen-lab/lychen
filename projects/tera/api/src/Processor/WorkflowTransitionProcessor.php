<?php

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Workflow\Registry;

readonly class WorkflowTransitionProcessor implements ProcessorInterface
{
    public function __construct(#[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')] private ProcessorInterface $persistProcessor, private Registry $registry)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $this->registry->get($data)->apply($data, $operation->getOptions()['transition']);

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
