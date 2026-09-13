<?php

namespace App\Tests\Functional;

use App\Factory\AreaActivityFactory;
use App\Factory\AreaProposalFactory;
use App\Factory\PersonFactory;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use App\Workflow\AreaProposal\AreaProposalWorkflow;

class AreaProposalTest extends AbstractApiTestCase
{
    public function testGetExposesProposerNameAndActivityCodes(): void
    {
        $proposer = PersonFactory::createOne(['givenName' => 'Ada', 'familyName' => 'Lovelace']);
        $proposal = AreaProposalFactory::createOne([
            'proposer' => $proposer,
            'place' => AreaProposalWorkflow::PLACE_PUBLISHED,
            'activities' => [AreaActivityFactory::createOne(['code' => 'gardening'])],
        ]);

        $this->browser()
            ->actingAs(PersonFactory::createOne())
            ->get('/api/area_proposals/'.$proposal->getUuid())
            ->assertStatus(200)
            ->assertJsonMatches('proposerFirstName', 'Ada')
            ->assertJsonMatches('proposerLastName', 'Lovelace')
            ->assertJsonMatches('activities', ['gardening'])
            ->assertJsonMatches('place', AreaProposalWorkflow::PLACE_PUBLISHED);
    }

    public function testGetArchivedProposalExposesArchivedAt(): void
    {
        $proposer = PersonFactory::createOne();
        $proposal = AreaProposalFactory::createOne([
            'proposer' => $proposer,
            'place' => AreaProposalWorkflow::PLACE_ARCHIVED,
            'archivedAt' => new \DateTimeImmutable('2026-09-01 10:00:00'),
        ]);

        $this->browser()
            ->actingAs($proposer)
            ->get('/api/area_proposals/'.$proposal->getUuid())
            ->assertStatus(200)
            ->assertJsonMatches("starts_with(archivedAt, '2026-09-01T10:00:00')", true);
    }

    public function testCollectionItemsUseTheResourceShape(): void
    {
        AreaProposalFactory::createOne([
            'proposer' => PersonFactory::createOne(['givenName' => 'Ada']),
            'place' => AreaProposalWorkflow::PLACE_PUBLISHED,
        ]);

        $this->browser()
            ->actingAs(PersonFactory::createOne())
            ->get('/api/area_proposals')
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 1)
            ->assertJsonMatches('member[0]."@type"', 'AreaProposal')
            ->assertJsonMatches('member[0].proposerFirstName', 'Ada');
    }

    public function testFilterByPlace(): void
    {
        $user = PersonFactory::createOne();
        AreaProposalFactory::createOne(['proposer' => $user, 'place' => AreaProposalWorkflow::PLACE_DRAFT]);
        AreaProposalFactory::createOne(['proposer' => $user, 'place' => AreaProposalWorkflow::PLACE_PUBLISHED]);
        AreaProposalFactory::createOne(['proposer' => PersonFactory::createOne(), 'place' => AreaProposalWorkflow::PLACE_PUBLISHED]);

        $this->browser()
            ->actingAs($user)
            ->get('/api/area_proposals?place=draft')
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 1);

        $this->browser()
            ->actingAs($user)
            ->get('/api/area_proposals?place=published')
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 2);
    }

    public function testFilterByPlaceRejectsUnknownPlace(): void
    {
        $this->browser()
            ->actingAs(PersonFactory::createOne())
            ->get('/api/area_proposals?place=a')
            ->assertStatus(422);
    }

    public function testFilterByActivityCode(): void
    {
        $proposer = PersonFactory::createOne();
        AreaProposalFactory::createOne([
            'proposer' => $proposer,
            'title' => 'Ruches',
            'activities' => [AreaActivityFactory::createOne(['code' => 'beehives'])],
        ]);
        AreaProposalFactory::createOne([
            'proposer' => $proposer,
            'activities' => [AreaActivityFactory::createOne(['code' => 'gardening'])],
        ]);

        $this->browser()
            ->actingAs($proposer)
            ->get('/api/area_proposals?activity=beehives')
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 1)
            ->assertJsonMatches('member[0].title', 'Ruches');
    }

    public function testFilterByCityIsPartialAndCaseInsensitive(): void
    {
        $proposer = PersonFactory::createOne();
        AreaProposalFactory::createOne(['proposer' => $proposer, 'city' => 'Grenoble']);
        AreaProposalFactory::createOne(['proposer' => $proposer, 'city' => 'Lyon']);

        $this->browser()
            ->actingAs($proposer)
            ->get('/api/area_proposals?city=GRENO')
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 1)
            ->assertJsonMatches('member[0].city', 'Grenoble');
    }

    public function testPostCreatesDraftForCurrentUser(): void
    {
        $user = PersonFactory::createOne(['givenName' => 'Ada', 'familyName' => 'Lovelace']);
        AreaActivityFactory::createOne(['code' => 'gardening']);

        $this->browser()
            ->actingAs($user)
            ->post('/api/area_proposals', ['json' => [
                'title' => 'Jardin partagé',
                'description' => 'Un grand jardin ensoleillé',
                'surfaceTotal' => 200,
                'surfaceToShare' => 50,
                'city' => 'Lyon',
                'altitude' => 170,
                'activities' => ['gardening', 'unknown'],
            ]])
            ->assertStatus(201)
            ->assertJsonMatches('title', 'Jardin partagé')
            ->assertJsonMatches('place', AreaProposalWorkflow::PLACE_DRAFT)
            ->assertJsonMatches('proposerFirstName', 'Ada')
            ->assertJsonMatches('activities', ['gardening']);

        AreaProposalFactory::assert()->count(1, ['proposer' => $user]);
    }

    public function testPostRejectsInvalidPayload(): void
    {
        $this->browser()
            ->actingAs(PersonFactory::createOne())
            ->post('/api/area_proposals', ['json' => [
                'title' => '',
                'description' => 'Un grand jardin',
                'surfaceTotal' => -5,
                'surfaceToShare' => 50,
                'altitude' => 170,
            ]])
            ->assertStatus(422)
            ->assertJsonMatches("length(violations[?propertyPath=='title'])", 1)
            ->assertJsonMatches("length(violations[?propertyPath=='surfaceTotal'])", 1)
            ->assertJsonMatches("length(violations[?propertyPath=='city'])", 1);

        AreaProposalFactory::assert()->empty();
    }

    public function testPatchOnlyUpdatesSentFields(): void
    {
        $proposer = PersonFactory::createOne();
        $proposal = AreaProposalFactory::createOne([
            'proposer' => $proposer,
            'title' => 'Avant',
            'description' => 'Inchangée',
            'activities' => [AreaActivityFactory::createOne(['code' => 'gardening'])],
        ]);

        $this->browser()
            ->actingAs($proposer)
            ->patch('/api/area_proposals/'.$proposal->getUuid(), ['json' => ['title' => 'Après']])
            ->assertStatus(200)
            ->assertJsonMatches('title', 'Après')
            ->assertJsonMatches('description', 'Inchangée')
            ->assertJsonMatches('activities', ['gardening']);
    }

    public function testPatchReplacesActivities(): void
    {
        $proposer = PersonFactory::createOne();
        $proposal = AreaProposalFactory::createOne([
            'proposer' => $proposer,
            'activities' => [AreaActivityFactory::createOne(['code' => 'gardening'])],
        ]);
        AreaActivityFactory::createOne(['code' => 'beehives']);

        $this->browser()
            ->actingAs($proposer)
            ->patch('/api/area_proposals/'.$proposal->getUuid(), ['json' => ['activities' => ['beehives']]])
            ->assertStatus(200)
            ->assertJsonMatches('activities', ['beehives']);
    }

    public function testPatchRejectsNullBlankAndInvalidValues(): void
    {
        $proposer = PersonFactory::createOne();
        $uri = '/api/area_proposals/'.AreaProposalFactory::createOne(['proposer' => $proposer])->getUuid();

        foreach ([['title' => null], ['title' => ''], ['surfaceTotal' => 0]] as $payload) {
            $this->browser()
                ->actingAs($proposer)
                ->patch($uri, ['json' => $payload])
                ->assertStatus(422);
        }
    }

    public function testDeleteOwnProposal(): void
    {
        $proposer = PersonFactory::createOne();
        $proposal = AreaProposalFactory::createOne(['proposer' => $proposer]);

        $this->browser()
            ->actingAs($proposer)
            ->delete('/api/area_proposals/'.$proposal->getUuid())
            ->assertStatus(204);

        AreaProposalFactory::assert()->empty();
    }
}
