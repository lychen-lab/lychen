<?php

namespace App\Doctrine\QueryExtension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Land;
use App\Entity\PersonApiKey;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class LandExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder,
        string $resourceClass): void
    {
        if (Land::class !== $resourceClass || $this->security->isGranted('ROLE_ADMIN') || null === $user = $this->security->getUser()) {
            return;
        }

        if ($user instanceof PersonApiKey) {
            $user = $user->getPerson();
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->innerJoin(sprintf('%s.landMembers', $rootAlias), 'lm', Join::WITH, $rootAlias.'.id = lm.land');
        $queryBuilder->andWhere('lm.person = :current_user');
        $queryBuilder->setParameter('current_user', $user->getId());
    }

    public function applyToItem(QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = []): void
    {
    }
}
