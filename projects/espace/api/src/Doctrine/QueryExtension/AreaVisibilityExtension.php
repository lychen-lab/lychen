<?php

namespace App\Doctrine\QueryExtension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use App\Entity\AreaProposal;
use App\Entity\AreaRequest;
use App\Entity\Person;
use App\Workflow\AreaProposal\AreaProposalWorkflow;
use App\Workflow\AreaRequest\AreaRequestWorkflow;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Restricts area proposals and requests to what the current user may reach:
 * reads see the public ones (published proposals, active requests) plus the
 * user's own, writes only reach the user's own. The restriction is applied in
 * SQL, so anything else answers 404 instead of revealing that it exists.
 */
final readonly class AreaVisibilityExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    /**
     * @var array<class-string, array{owner: string, publicPlace: string}>
     */
    private const array RULES = [
        AreaProposal::class => ['owner' => 'proposer', 'publicPlace' => AreaProposalWorkflow::PLACE_PUBLISHED],
        AreaRequest::class => ['owner' => 'requester', 'publicPlace' => AreaRequestWorkflow::PLACE_ACTIVE],
    ];

    private const array READ_METHODS = [HttpOperation::METHOD_GET, HttpOperation::METHOD_HEAD];

    public function __construct(private Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->restrict($queryBuilder, $queryNameGenerator, $resourceClass, $operation);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
        $this->restrict($queryBuilder, $queryNameGenerator, $resourceClass, $operation);
    }

    private function restrict(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation): void
    {
        $rule = self::RULES[$resourceClass] ?? null;
        if (null === $rule) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user instanceof Person) {
            $queryBuilder->andWhere('1 = 0');

            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $ownerParameter = $queryNameGenerator->generateParameterName('owner');
        $isOwner = \sprintf('%s.%s = :%s', $alias, $rule['owner'], $ownerParameter);
        $queryBuilder->setParameter($ownerParameter, $user);

        if ($operation instanceof HttpOperation && !\in_array($operation->getMethod(), self::READ_METHODS, true)) {
            $queryBuilder->andWhere($isOwner);

            return;
        }

        $placeParameter = $queryNameGenerator->generateParameterName('place');
        $queryBuilder
            ->andWhere($queryBuilder->expr()->orX($isOwner, \sprintf('%s.place = :%s', $alias, $placeParameter)))
            ->setParameter($placeParameter, $rule['publicPlace']);
    }
}
