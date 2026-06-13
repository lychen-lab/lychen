<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use App\Entity\Land;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class LandFilter extends AbstractFilter
{
    public function __construct(ManagerRegistry $managerRegistry,
        ?LoggerInterface $logger = null,
        ?array $properties = null,
        ?NameConverterInterface $nameConverter = null,
    )
    {
        parent::__construct($managerRegistry, $logger, $properties, $nameConverter);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'land' => [
                'property' => 'land',
                'type' => 'string',
                'openApi' => new Parameter(
                    name           : 'land',
                    in             : 'query',
                    required       : true,
                    allowEmptyValue: false,
                    explode        : false
                ),
                'required' => true,
                'description' => 'Filter by land using its IRI.',
            ]
        ];
    }

    protected function filterProperty(string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []): void
    {
        if ('land' !== $property) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];

        $land = $this->managerRegistry->getRepository(Land::class)->findOneBy(['ulid' => $value]);

        if (!$land instanceof Land) {
            return;
        }

        $queryBuilder
            ->andWhere(sprintf('%s.land = :land', $alias))
            ->setParameter('land', $land);
    }
}
