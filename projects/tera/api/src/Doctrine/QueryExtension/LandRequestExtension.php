<?php

namespace App\Doctrine\QueryExtension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\LandRequest;
use App\Workflow\LandRequest\LandRequestWorkflowPlace;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class LandRequestExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        if ($operation->getName() && !str_contains($operation->getName(), 'public')) {
            $this->addWhere($queryBuilder, $resourceClass);
        } else {
            $this->addWhereForPublic($queryBuilder, $resourceClass);
        }
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (LandRequest::class !== $resourceClass || $this->security->isGranted('ROLE_ADMIN') || null === $user = $this->security->getUser()) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.person = :current_user', $rootAlias));
        $queryBuilder->setParameter('current_user', $user->getId());
    }

    private function addWhereForPublic(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (LandRequest::class !== $resourceClass || $this->security->isGranted('ROLE_ADMIN') || null === $user = $this->security->getUser()) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->andWhere(sprintf('%s.state = :state', $rootAlias));
        $queryBuilder->andWhere(sprintf('%s.person != :current_user', $rootAlias));
        $queryBuilder->setParameter('current_user', $user->getId());
        $queryBuilder->setParameter('state', LandRequestWorkflowPlace::PUBLISHED);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
    }
}
