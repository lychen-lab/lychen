<?php

namespace App\Tests;

use App\Entity\AreaListingInterface;
use App\Entity\Person;
use App\Factory\AreaProposalFactory;
use App\Factory\AreaRequestFactory;
use App\Workflow\AreaProposal\AreaProposalWorkflow;
use App\Workflow\AreaRequest\AreaRequestWorkflow;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * One of the two listings (area proposal, area request), for the tests of what they have
 * in common: their lifecycle and its access rules. Used as a data provider, so it only
 * holds scalars: factories cannot run before the kernel boots.
 */
final readonly class AreaListingCase
{
    public const string SUBMIT = 'submit';
    public const string REJECT = 'reject';
    public const string ARCHIVE = 'archive';

    /**
     * @param class-string<PersistentObjectFactory<covariant AreaListingInterface>> $factory
     * @param list<string>                                                          $places
     * @param array<string, array{from: list<string>, to: string}>                  $transitions
     */
    private function __construct(
        public string $collection,
        private string $factory,
        private string $owner,
        public string $pending,
        public string $public,
        public string $accept,
        public array $places,
        public array $transitions,
    ) {
    }

    /**
     * @return iterable<string, array{self}>
     */
    public static function all(): iterable
    {
        yield 'area proposal' => [new self(
            collection: '/api/area_proposals',
            factory: AreaProposalFactory::class,
            owner: 'proposer',
            pending: AreaProposalWorkflow::PLACE_VERIFICATION,
            public: AreaProposalWorkflow::PLACE_PUBLISHED,
            accept: AreaProposalWorkflow::TRANSITION_PUBLISH,
            places: AreaProposalWorkflow::PLACES,
            transitions: [
                AreaProposalWorkflow::TRANSITION_SUBMIT => ['from' => ['draft'], 'to' => 'verification'],
                AreaProposalWorkflow::TRANSITION_PUBLISH => ['from' => ['verification'], 'to' => 'published'],
                AreaProposalWorkflow::TRANSITION_REJECT => ['from' => ['verification'], 'to' => 'draft'],
                AreaProposalWorkflow::TRANSITION_ARCHIVE => ['from' => ['verification', 'published'], 'to' => 'archived'],
            ],
        )];

        yield 'area request' => [new self(
            collection: '/api/area_requests',
            factory: AreaRequestFactory::class,
            owner: 'requester',
            pending: AreaRequestWorkflow::PLACE_PENDING_VALIDATION,
            public: AreaRequestWorkflow::PLACE_ACTIVE,
            accept: AreaRequestWorkflow::TRANSITION_APPROVE,
            places: AreaRequestWorkflow::PLACES,
            transitions: [
                AreaRequestWorkflow::TRANSITION_SUBMIT => ['from' => ['draft'], 'to' => 'pending_validation'],
                AreaRequestWorkflow::TRANSITION_APPROVE => ['from' => ['pending_validation'], 'to' => 'active'],
                AreaRequestWorkflow::TRANSITION_REJECT => ['from' => ['pending_validation'], 'to' => 'draft'],
                AreaRequestWorkflow::TRANSITION_ARCHIVE => ['from' => ['pending_validation', 'active'], 'to' => 'archived'],
            ],
        )];
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(Person $owner, string $place, array $attributes = []): AreaListingInterface
    {
        return $this->factory::createOne([$this->owner => $owner, 'place' => $place] + $attributes);
    }

    /**
     * The item's URI of a new listing.
     *
     * @param array<string, mixed> $attributes
     */
    public function uri(Person $owner, string $place, array $attributes = []): string
    {
        $listing = $this->create($owner, $place, $attributes);
        \assert(method_exists($listing, 'getUuid'));

        return $this->collection.'/'.$listing->getUuid();
    }

    /**
     * The request options of a transition: only a rejection has a body.
     *
     * @return array{json: array<string, string>}
     */
    public static function body(string $transition): array
    {
        return ['json' => self::REJECT === $transition ? ['rejectionReason' => 'Photos manquantes.'] : []];
    }
}
