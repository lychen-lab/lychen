<?php

namespace App\Provider;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\ProviderInterface;
use App\Repository\LandRepository;

final readonly class LandsLookingForMembersProvider implements ProviderInterface
{
    public function __construct(
        private LandRepository $landRepository,
        private Pagination $pagination,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        [$page, , $limit] = $this->pagination->getPagination($operation, $context);

        return new Paginator($this->landRepository->findLookingForMembers($page, $limit));
    }
}
