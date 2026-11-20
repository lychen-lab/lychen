<?php

namespace App\Doctrine\QueryExtension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\LandRequest;
use App\Entity\Person;
use App\Security\Service\ActingPersonResolver;
use App\Workflow\LandRequest\LandRequestWorkflowPlace;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class LandRequestExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private Security $security,
        private ActingPersonResolver $actingPersonResolver,
    ) {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        if (LandRequest::class !== $resourceClass || $this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        // Both lists are relative to a person, which a LandApiKey does not stand for: it sees neither
        if (null === $person = $this->actingPersonResolver->resolve()) {
            $queryBuilder->andWhere('1 = 0');

            return;
        }

        if ($operation?->getName() && !str_contains($operation->getName(), 'public')) {
            $this->addWhere($queryBuilder, $rootAlias, $person);
        } else {
            $this->addWhereForPublic($queryBuilder, $rootAlias, $person);
        }
    }

    /**
     * The person's own requests.
     */
    private function addWhere(QueryBuilder $queryBuilder, string $rootAlias, Person $person): void
    {
        $queryBuilder->andWhere(sprintf('%s.person = :current_user', $rootAlias));
        $queryBuilder->setParameter('current_user', $person);
    }

    /**
     * The others' published requests.
     */
    private function addWhereForPublic(QueryBuilder $queryBuilder, string $rootAlias, Person $person): void
    {
        $queryBuilder->andWhere(sprintf('%s.state = :state', $rootAlias));
        $queryBuilder->andWhere(sprintf('%s.person != :current_user', $rootAlias));
        $queryBuilder->setParameter('current_user', $person);
        $queryBuilder->setParameter('state', LandRequestWorkflowPlace::PUBLISHED);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
    }
}
