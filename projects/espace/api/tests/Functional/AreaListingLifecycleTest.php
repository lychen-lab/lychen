<?php

namespace App\Tests\Functional;

use App\Entity\Person;
use App\Factory\PersonFactory;
use App\Security\Role;
use App\Tests\AreaListingCase;
use Lychen\Test\AbstractApiTestCase;
use PHPUnit\Framework\Attributes\DataProviderExternal;

/**
 * The lifecycle area proposals and area requests share: the owner submits a draft,
 * moderation accepts it or sends it back with a reason, the owner archives it. Who may
 * do what is covered by AreaListingModerationSecurityTest.
 */
class AreaListingLifecycleTest extends AbstractApiTestCase
{
    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testOwnerSubmitsThenModerationAccepts(AreaListingCase $case): void
    {
        $owner = PersonFactory::createOne();
        $uri = $case->uri($owner, 'draft');

        $this->browser()
            ->actingAs($owner)
            ->patch($uri.'/submit')
            ->assertStatus(200)
            // The listing itself, under its own IRI, not the transition's.
            ->assertJsonMatches('"@id"', $uri)
            ->assertJsonMatches('place', $case->pending);

        $this->browser()
            ->actingAs(self::moderator())
            ->patch($uri.'/'.$case->accept)
            ->assertStatus(200)
            ->assertJsonMatches('"@id"', $uri)
            ->assertJsonMatches('place', $case->public);

        // Which is what makes it visible to everyone.
        $this->browser()
            ->actingAs(PersonFactory::createOne())
            ->get($uri)
            ->assertStatus(200)
            ->assertJsonMatches('place', $case->public);
    }

    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testRejectedListingIsFixedThenSubmittedAgain(AreaListingCase $case): void
    {
        $owner = PersonFactory::createOne();
        $uri = $case->uri($owner, $case->pending, ['title' => 'Avant']);

        $this->browser()
            ->actingAs(self::moderator())
            ->patch($uri.'/reject', ['json' => ['rejectionReason' => 'Précisez la surface.']])
            ->assertStatus(200)
            ->assertJsonMatches('place', 'draft')
            ->assertJsonMatches('rejectionReason', 'Précisez la surface.');

        // The owner reads why, and it is a draft again: editable.
        $this->browser()
            ->actingAs($owner)
            ->get($uri)
            ->assertStatus(200)
            ->assertJsonMatches('rejectionReason', 'Précisez la surface.');

        $this->browser()
            ->actingAs($owner)
            ->patch($uri, ['json' => ['title' => 'Après']])
            ->assertStatus(200)
            ->assertJsonMatches('title', 'Après')
            ->assertJsonMatches('rejectionReason', 'Précisez la surface.');

        // Submitting again clears the reason.
        $submitted = $this->browser()
            ->actingAs($owner)
            ->patch($uri.'/submit')
            ->assertStatus(200)
            ->assertJsonMatches('place', $case->pending)
            ->json()->decoded();

        self::assertArrayNotHasKey('rejectionReason', $submitted);
    }

    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testRejectionNeedsAReason(AreaListingCase $case): void
    {
        $owner = PersonFactory::createOne();
        $uri = $case->uri($owner, $case->pending);

        foreach ([[], ['rejectionReason' => ''], ['rejectionReason' => '   '], ['rejectionReason' => str_repeat('a', 2001)]] as $payload) {
            $this->browser()
                ->actingAs(self::moderator())
                ->patch($uri.'/reject', ['json' => $payload])
                ->assertStatus(422)
                ->assertJsonMatches("length(violations[?propertyPath=='rejectionReason'])", 1);
        }

        $this->browser()
            ->actingAs($owner)
            ->get($uri)
            ->assertJsonMatches('place', $case->pending);
    }

    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testArchivingDatesTheListing(AreaListingCase $case): void
    {
        $owner = PersonFactory::createOne();

        foreach ([$case->pending, $case->public] as $place) {
            $before = new \DateTimeImmutable('-1 minute');

            $archived = $this->browser()
                ->actingAs($owner)
                ->patch($case->uri($owner, $place).'/archive')
                ->assertStatus(200)
                ->assertJsonMatches('place', 'archived')
                ->json()->decoded();

            self::assertGreaterThan($before, new \DateTimeImmutable($archived['archivedAt']));
        }
    }

    /**
     * Every transition from every place, by a moderator on their own listing, the one
     * person all four transitions reach: 200 where the workflow allows it, 409 elsewhere.
     */
    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testTransitionThePlaceDoesNotAllowIsAConflict(AreaListingCase $case): void
    {
        $moderator = self::moderator();

        foreach ($case->places as $place) {
            foreach ($case->transitions as $transition => ['from' => $from, 'to' => $to]) {
                $uri = $case->uri($moderator, $place);
                $allowed = \in_array($place, $from, true);

                $this->browser()
                    ->actingAs($moderator)
                    ->patch($uri.'/'.$transition, AreaListingCase::body($transition))
                    ->assertStatus($allowed ? 200 : 409);

                $listing = $this->browser()
                    ->actingAs($moderator)
                    ->get($uri)
                    ->assertStatus(200)
                    ->assertJsonMatches('place', $allowed ? $to : $place)
                    ->json()->decoded();

                if (!$allowed) {
                    // Nothing of a refused transition is kept, its reason included.
                    self::assertArrayNotHasKey('rejectionReason', $listing, \sprintf('%s from %s', $transition, $place));
                    self::assertArrayNotHasKey('archivedAt', $listing, \sprintf('%s from %s', $transition, $place));
                }
            }
        }
    }

    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testContentCanOnlyBeEditedAsADraft(AreaListingCase $case): void
    {
        $owner = PersonFactory::createOne();

        foreach ($case->places as $place) {
            $uri = $case->uri($owner, $place, ['title' => 'Avant']);
            $editable = 'draft' === $place;

            $this->browser()
                ->actingAs($owner)
                ->patch($uri, ['json' => ['title' => 'Après']])
                ->assertStatus($editable ? 200 : 409);

            $this->browser()
                ->actingAs($owner)
                ->get($uri)
                ->assertJsonMatches('title', $editable ? 'Après' : 'Avant');
        }
    }

    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testMineKeepsTheUsersOwnListings(AreaListingCase $case): void
    {
        $user = PersonFactory::createOne();
        $other = PersonFactory::createOne();
        $case->create($user, 'draft');
        $case->create($user, $case->public);
        $case->create($other, $case->public);
        $case->create($other, 'draft');

        $total = fn (string $query): int => $this->browser()
            ->actingAs($user)
            ->get($case->collection.$query)
            ->assertStatus(200)
            ->json()->decoded()['totalItems'];

        self::assertSame(3, $total(''));
        self::assertSame(2, $total('?mine=true'));
        self::assertSame(1, $total('?mine=true&place=draft'));
        self::assertSame(3, $total('?mine=false'));
    }

    #[DataProviderExternal(AreaListingCase::class, 'all')]
    public function testPlaceFilterGivesModeratorsTheirQueue(AreaListingCase $case): void
    {
        $moderator = self::moderator();
        $case->create(PersonFactory::createOne(), $case->pending);
        $case->create(PersonFactory::createOne(), $case->pending);
        $case->create($moderator, $case->pending);
        $case->create(PersonFactory::createOne(), $case->public);
        $case->create(PersonFactory::createOne(), 'draft');

        $this->browser()
            ->actingAs($moderator)
            ->get($case->collection.'?place='.$case->pending)
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 3);
    }

    private static function moderator(): Person
    {
        return PersonFactory::createOne(['roles' => [Role::MODERATOR]]);
    }
}
