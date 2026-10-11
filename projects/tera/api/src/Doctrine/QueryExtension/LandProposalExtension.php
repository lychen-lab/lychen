<?php

namespace App\Doctrine\QueryExtension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\LandProposal;
use App\Entity\Person;
use App\Entity\PersonApiKey;
use App\Repository\LandMemberRepository;
use App\Workflow\LandProposal\LandProposalWorkflowPlace;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class LandProposalExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private Security $security,
        private LandMemberRepository $landMemberRepository,
    ) {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        // Only apply this logic to LandProposal entities
        if (LandProposal::class !== $resourceClass) {
            return;
        }

        if ('/land_proposals/public' === $operation?->getUriTemplate()) {
            $this->addWhereForPublicCollection($queryBuilder);
        }
    }

    private function addWhereForPublicCollection(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        // 1. Filter by state: Only show PUBLISHED proposals publicly
        $queryBuilder->andWhere(sprintf('%s.state = :publicState', $rootAlias));
        $queryBuilder->setParameter('publicState', LandProposalWorkflowPlace::PUBLISHED);

        // 2. Filter out proposals linked to lands the user is a member of
        $user = $this->security->getUser();

        if ($user instanceof PersonApiKey) {
            $user = $user->getPerson();
        }

        // Proceed only if the user is logged in and is a standard user (not admin, as admins might need to see all)
        // If you want admins to also be filtered, remove the !isGranted('ROLE_ADMIN') check
        if ($user instanceof Person && !$this->security->isGranted('ROLE_ADMIN')) {
            // Find Land IDs where the user is a member
            $userLandIds = $this->landMemberRepository->findPersonLandIds($user);

            // If the user is a member of some lands, exclude proposals from those lands
            if (!empty($userLandIds)) {
                // Ensure the 'land' association is joined or accessible
                // Doctrine often handles this automatically, but explicit join can be added if needed:
                // $queryBuilder->leftJoin(sprintf('%s.land', $rootAlias), 'l');
                // $queryBuilder->andWhere($queryBuilder->expr()->notIn('l.id', ':userLandIds'));

                // Using the direct association path is usually sufficient:
                $queryBuilder->andWhere($queryBuilder->expr()->notIn(sprintf('%s.land', $rootAlias), ':userLandIds'));
                $queryBuilder->setParameter('userLandIds', $userLandIds);
            }
            // If $userLandIds is empty, the user is not a member of any land,
            // so no further filtering based on land membership is needed.
        }
    }

    /**
     * Apply conditions to item operations if needed.
     * Currently, no specific logic for item operations in this extension.
     * Access control is handled by Voters.
     */
    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        // If you needed to restrict item access based on similar criteria,
        // you could add logic here, but usually, Voters are better suited for item operations.
        // Example: Ensure a user can only GET their own DRAFT proposal via the standard GET endpoint.
    }
}
