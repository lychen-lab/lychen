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
use App\Security\Role;
use App\Workflow\AreaProposal\AreaProposalWorkflow;
use App\Workflow\AreaRequest\AreaRequestWorkflow;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Restricts area proposals and requests to what the current user may reach. The
 * restriction is applied in SQL, so anything else answers 404 instead of revealing that
 * it exists.
 *
 * - Reads see the public listings (published proposals, active requests) plus the
 *   user's own; a moderator also sees the ones waiting for moderation.
 * - Writes — content, deletion, and the owner's transitions (submit, archive) — only
 *   reach the user's own.
 * - Moderation transitions (the workflow's MODERATION_TRANSITIONS) reach nothing for
 *   whoever is not a moderator, and what a moderator may read otherwise: a listing they
 *   see but whose place does not allow the transition is then a 409, not a 404.
 */
final readonly class AreaVisibilityExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    /**
     * @var array<class-string, array{owner: string, publicPlace: string, pendingPlace: string, moderationTransitions: list<string>}>
     */
    private const array RULES = [
        AreaProposal::class => [
            'owner' => 'proposer',
            'publicPlace' => AreaProposalWorkflow::PLACE_PUBLISHED,
            'pendingPlace' => AreaProposalWorkflow::PLACE_VERIFICATION,
            'moderationTransitions' => AreaProposalWorkflow::MODERATION_TRANSITIONS,
        ],
        AreaRequest::class => [
            'owner' => 'requester',
            'publicPlace' => AreaRequestWorkflow::PLACE_ACTIVE,
            'pendingPlace' => AreaRequestWorkflow::PLACE_PENDING_VALIDATION,
            'moderationTransitions' => AreaRequestWorkflow::MODERATION_TRANSITIONS,
        ],
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

        $isModerator = $this->security->isGranted(Role::MODERATOR);
        $isWrite = $operation instanceof HttpOperation && !\in_array($operation->getMethod(), self::READ_METHODS, true);
        $isModeration = $isWrite && \in_array($operation->getOptions()['transition'] ?? null, $rule['moderationTransitions'], true);

        if ($isModeration && !$isModerator) {
            $queryBuilder->andWhere('1 = 0');

            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $ownerParameter = $queryNameGenerator->generateParameterName('owner');
        $isOwner = \sprintf('%s.%s = :%s', $alias, $rule['owner'], $ownerParameter);
        $queryBuilder->setParameter($ownerParameter, $user);

        if ($isWrite && !$isModeration) {
            $queryBuilder->andWhere($isOwner);

            return;
        }

        $placesParameter = $queryNameGenerator->generateParameterName('places');
        $queryBuilder
            ->andWhere($queryBuilder->expr()->orX($isOwner, \sprintf('%s.place IN (:%s)', $alias, $placesParameter)))
            ->setParameter($placesParameter, $isModerator ? [$rule['publicPlace'], $rule['pendingPlace']] : [$rule['publicPlace']]);
    }
}
