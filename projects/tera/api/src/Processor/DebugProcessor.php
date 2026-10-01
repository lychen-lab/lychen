<?php

namespace App\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/** Only for dev */
class DebugProcessor implements ProcessorInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        try {
            $this->entityManager->remove($data);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            if (method_exists($e, 'getCycle')) {
                $cycleNodes = $e->getCycle();
                // Affiche ou traite le cycle
                // dd($cycleNodes);
            }
            throw $e; // ou gère autrement l'exception
        }
    }
}
