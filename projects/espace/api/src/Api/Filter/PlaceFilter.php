<?php

namespace App\Api\Filter;


use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

final class PlaceFilter implements FilterInterface
{
    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $parameter = $context['parameter'];
        $value = $parameter->getValue();

        $property = 'place';

        $alias = $queryBuilder->getRootAliases()[0];
        $field = $alias . '.' . $property;

        $parameterName = $queryNameGenerator->generateParameterName($property);

        $queryBuilder
            ->andWhere($queryBuilder->expr()->like('LOWER(' . $field . ')', ':' . $parameterName))
            ->setParameter($parameterName, '%' . strtolower($value) . '%');
    }

    public function getDescription(string $resourceClass): array
    {
        return [];
    }
}
