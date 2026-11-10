<?php

namespace App\Tests\Security;

use App\Entity\Person;
use App\Factory\PersonFactory;
use App\Security\Role;
use App\Tests\AreaListingCase;
use Lychen\Test\AbstractApiTestCase;
use PHPUnit\Framework\Attributes\DataProviderExternal;

/**
 * Who reaches which transition of an area proposal or request, and who reads what on the
 * way: the owner submits and archives, moderators decide on what is pending, and
 * whatever a user may not reach answers 404.
 */
class AreaListingModerationSecurityTest extends AbstractApiTestCase
{
    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testAnonymousUserIsDenied(AreaListingCase $case): void
    {
        $uri = $case->uri(PersonFactory::createOne(), $case->pending);

        foreach (array_keys($case->transitions) as $transition) {
            $this->browser()
                ->patch($uri.'/'.$transition, AreaListingCase::body($transition))
                ->assertStatus(401);
        }
    }

    /**
     * Someone else's listing, even one they can read: no transition, from any place.
     */
    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testThirdPartyReachesNoTransition(AreaListingCase $case): void
    {
        $owner = PersonFactory::createOne();
        // Another domain's role must not count as moderation.
        $thirdParties = [PersonFactory::createOne(), PersonFactory::createOne(['roles' => ['ROLE_ADMIN']])];

        foreach ($case->places as $place) {
            $uri = $case->uri($owner, $place);

            foreach ($thirdParties as $thirdParty) {
                foreach (array_keys($case->transitions) as $transition) {
                    $this->browser()
                        ->actingAs($thirdParty)
                        ->patch($uri.'/'.$transition, AreaListingCase::body($transition))
                        ->assertStatus(404);
                }
            }

            $this->assertPlace($case, $owner, $uri, $place);
        }
    }

    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testOwnerCannotModerateTheirOwnListing(AreaListingCase $case): void
    {
        $owner = PersonFactory::createOne();
        $uri = $case->uri($owner, $case->pending);

        foreach ([$case->accept, AreaListingCase::REJECT] as $transition) {
            $this->browser()
                ->actingAs($owner)
                ->patch($uri.'/'.$transition, AreaListingCase::body($transition))
                ->assertStatus(404);
        }

        $this->assertPlace($case, $owner, $uri, $case->pending);
    }

    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testModeratorReadsAndDecidesOnPendingListingsOfOthers(AreaListingCase $case): void
    {
        $owner = PersonFactory::createOne();
        $moderator = self::moderator();

        foreach ([$case->accept => $case->public, AreaListingCase::REJECT => 'draft'] as $transition => $place) {
            $uri = $case->uri($owner, $case->pending);

            $this->browser()->actingAs($moderator)->get($uri)->assertStatus(200);
            $this->browser()->actingAs($moderator)->get($uri.'/workflow')->assertStatus(200);
            $this->browser()
                ->actingAs($moderator)
                ->patch($uri.'/'.$transition, AreaListingCase::body($transition))
                ->assertStatus(200)
                ->assertJsonMatches('place', $place);
        }
    }

    /**
     * Moderation opens the pending listings to reading and to its two decisions, nothing
     * else: no edit, no deletion, none of the owner's transitions.
     */
    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testModeratorCannotWriteOnPendingListingsOfOthers(AreaListingCase $case): void
    {
        $owner = PersonFactory::createOne();
        $moderator = self::moderator();
        $uri = $case->uri($owner, $case->pending);

        $this->browser()->actingAs($moderator)->patch($uri, ['json' => ['title' => 'Piraté']])->assertStatus(404);
        $this->browser()->actingAs($moderator)->delete($uri)->assertStatus(404);
        $this->browser()->actingAs($moderator)->patch($uri.'/'.AreaListingCase::SUBMIT)->assertStatus(404);
        $this->browser()->actingAs($moderator)->patch($uri.'/'.AreaListingCase::ARCHIVE)->assertStatus(404);

        $this->assertPlace($case, $owner, $uri, $case->pending);
    }

    /**
     * A moderator sees no more of the other places than anyone: what they cannot read is
     * a 404, what they can read but is not pending is a 409.
     */
    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testModeratorReachesNothingElseOfOthers(AreaListingCase $case): void
    {
        $owner = PersonFactory::createOne();
        $moderator = self::moderator();

        foreach ($case->places as $place) {
            if ($place === $case->pending) {
                continue;
            }

            $uri = $case->uri($owner, $place);
            $isPublic = $place === $case->public;

            $this->browser()->actingAs($moderator)->get($uri)->assertStatus($isPublic ? 200 : 404);

            foreach ([$case->accept, AreaListingCase::REJECT] as $transition) {
                $this->browser()
                    ->actingAs($moderator)
                    ->patch($uri.'/'.$transition, AreaListingCase::body($transition))
                    ->assertStatus($isPublic ? 409 : 404);
            }

            $this->assertPlace($case, $owner, $uri, $place);
        }

        $this->browser()
            ->actingAs($moderator)
            ->get($case->collection)
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 1);
    }

    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testPendingListingsAreOnlyListedToTheirOwnerAndModerators(AreaListingCase $case): void
    {
        $owner = PersonFactory::createOne();
        $case->create($owner, $case->pending);
        $case->create(PersonFactory::createOne(), $case->pending);
        $query = $case->collection.'?place='.$case->pending;

        $this->browser()->actingAs(self::moderator())->get($query)->assertJsonMatches('totalItems', 2);
        $this->browser()->actingAs($owner)->get($query)->assertJsonMatches('totalItems', 1);
        $this->browser()->actingAs(PersonFactory::createOne())->get($query)->assertJsonMatches('totalItems', 0);
    }

    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testRejectionReasonIsOnlyReadByTheOwner(AreaListingCase $case): void
    {
        $owner = PersonFactory::createOne();
        $uri = $case->uri($owner, $case->pending);

        $this->browser()
            ->actingAs(self::moderator())
            ->patch($uri.'/reject', ['json' => ['rejectionReason' => 'Annonce incomplète.']])
            ->assertStatus(200);

        // Rejected, it is a draft: out of reach of anyone but its owner, in an item as in
        // a collection, another moderator included.
        foreach ([PersonFactory::createOne(), self::moderator()] as $other) {
            $this->browser()->actingAs($other)->get($uri)->assertStatus(404);

            $content = $this->browser()->actingAs($other)->get($case->collection)->assertStatus(200)->content();
            self::assertStringNotContainsString('Annonce incomplète.', $content);
        }

        $this->browser()->actingAs($owner)->get($uri)->assertJsonMatches('rejectionReason', 'Annonce incomplète.');

        // And once it is public, the reason is gone.
        $this->browser()->actingAs($owner)->patch($uri.'/submit')->assertStatus(200);
        $this->browser()->actingAs(self::moderator())->patch($uri.'/'.$case->accept)->assertStatus(200);

        $public = $this->browser()->actingAs(PersonFactory::createOne())->get($uri)->assertStatus(200)->json()->decoded();
        self::assertArrayNotHasKey('rejectionReason', $public);
    }

    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testMineDoesNotWidenWhatIsListed(AreaListingCase $case): void
    {
        $case->create(PersonFactory::createOne(), $case->public);
        $case->create(PersonFactory::createOne(), 'draft');

        $this->browser()
            ->actingAs(PersonFactory::createOne())
            ->get($case->collection.'?mine=true')
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 0);
    }

    private function assertPlace(AreaListingCase $case, Person $owner, string $uri, string $place): void
    {
        $this->browser()->actingAs($owner)->get($uri)->assertStatus(200)->assertJsonMatches('place', $place);
    }

    private static function moderator(): Person
    {
        return PersonFactory::createOne(['roles' => [Role::MODERATOR]]);
    }
}
