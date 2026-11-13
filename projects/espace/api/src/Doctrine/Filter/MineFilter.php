<?php

namespace App\Doctrine\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Parameter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * `?mine=true`: keeps what the current user owns, the parameter's `property` naming the
 * owner association. Any other value leaves the collection as it is.
 *
 *     'mine' => new QueryParameter(
 *         schema: ['type' => 'boolean'],
 *         filter: MineFilter::class,
 *         property: 'proposer',
 *     )
 */
final readonly class MineFilter implements FilterInterface
{
    public function __construct(private Security $security)
    {
    }

    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $parameter = $context['parameter'] ?? null;
        if (!$parameter instanceof Parameter || true !== filter_var($parameter->getValue(), \FILTER_VALIDATE_BOOL)) {
            return;
        }

        $property = $parameter->getProperty() ?? throw new InvalidArgumentException(\sprintf('The filter parameter with key "%s" must specify a property: the owner association.', $parameter->getKey()));

        $user = $this->security->getUser();
        if (null === $user) {
            $queryBuilder->andWhere('1 = 0');

            return;
        }

        $parameterName = $queryNameGenerator->generateParameterName($property);
        $queryBuilder
            ->andWhere(\sprintf('%s.%s = :%s', $queryBuilder->getRootAliases()[0], $property, $parameterName))
            ->setParameter($parameterName, $user);
    }

    public function getDescription(string $resourceClass): array
    {
        return [];
    }
}
