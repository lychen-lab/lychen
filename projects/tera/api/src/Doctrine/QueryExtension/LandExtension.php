<?php

namespace App\Doctrine\QueryExtension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Land;
use App\Security\Service\ActingPersonResolver;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class LandExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private Security $security,
        private ActingPersonResolver $actingPersonResolver,
    ) {
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
        if (Land::class !== $resourceClass || $this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        // The lands of a person: none to a LandApiKey, which stands for no person (and which the
        // LandVoter::COLLECTION permission, never given to one, already keeps off the list)
        if (null === $person = $this->actingPersonResolver->resolve()) {
            $queryBuilder->andWhere('1 = 0');

            return;
        }

        $queryBuilder->innerJoin(sprintf('%s.landMembers', $rootAlias), 'lm', Join::WITH, $rootAlias.'.id = lm.land');
        $queryBuilder->andWhere('lm.person = :current_user');
        $queryBuilder->setParameter('current_user', $person);
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
