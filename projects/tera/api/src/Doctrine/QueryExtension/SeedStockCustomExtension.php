<?php

namespace App\Doctrine\QueryExtension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Person;
use App\Entity\SeedStock;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class SeedStockCustomExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (SeedStock::class !== $resourceClass || $this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $user = $this->security->getUser();

        // A person's own seed stocks, to the person alone, as SeedStock's item operations do: no
        // permission gives an API key access to seed stocks (none is in PersonPermission::ALL), so a
        // PersonApiKey sees none, nor does a LandApiKey, which stands for no person.
        if (!$user instanceof Person) {
            $queryBuilder->andWhere('1 = 0');

            return;
        }

        $queryBuilder->andWhere(sprintf('%s.person = :current_user', $rootAlias));
        $queryBuilder->setParameter('current_user', $user);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
    }
}
