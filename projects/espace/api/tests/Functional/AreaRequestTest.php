<?php

namespace App\Tests\Functional;

use App\Factory\AreaActivityFactory;
use App\Factory\AreaRequestFactory;
use App\Factory\PersonFactory;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use App\Workflow\AreaRequest\AreaRequestWorkflow;

class AreaRequestTest extends AbstractApiTestCase
{
    public function testGetExposesRequesterNameAndActivityCodes(): void
    {
        $requester = PersonFactory::createOne(['givenName' => 'Grace', 'familyName' => 'Hopper']);
        $areaRequest = AreaRequestFactory::createOne([
            'requester' => $requester,
            'place' => AreaRequestWorkflow::PLACE_ACTIVE,
            'activities' => [AreaActivityFactory::createOne(['code' => 'gardening'])],
        ]);

        $this->browser()
            ->actingAs(PersonFactory::createOne())
            ->get('/api/area_requests/'.$areaRequest->getUuid())
            ->assertStatus(200)
            ->assertJsonMatches('requesterFirstName', 'Grace')
            ->assertJsonMatches('requesterLastName', 'Hopper')
            ->assertJsonMatches('activities', ['gardening'])
            ->assertJsonMatches('place', AreaRequestWorkflow::PLACE_ACTIVE);
    }

    public function testGetArchivedRequestExposesArchivedAt(): void
    {
        $requester = PersonFactory::createOne();
        $areaRequest = AreaRequestFactory::createOne([
            'requester' => $requester,
            'place' => AreaRequestWorkflow::PLACE_ARCHIVED,
            'archivedAt' => new \DateTimeImmutable('2026-09-01 10:00:00'),
        ]);

        $this->browser()
            ->actingAs($requester)
            ->get('/api/area_requests/'.$areaRequest->getUuid())
            ->assertStatus(200)
            ->assertJsonMatches("starts_with(archivedAt, '2026-09-01T10:00:00')", true);
    }

    public function testFilterByPlaceAndActivityCode(): void
    {
        $requester = PersonFactory::createOne();
        AreaRequestFactory::createOne([
            'requester' => $requester,
            'place' => AreaRequestWorkflow::PLACE_ACTIVE,
            'title' => 'Ruches',
            'activities' => [AreaActivityFactory::createOne(['code' => 'beehives'])],
        ]);
        AreaRequestFactory::createOne([
            'requester' => $requester,
            'place' => AreaRequestWorkflow::PLACE_DRAFT,
            'activities' => [AreaActivityFactory::createOne(['code' => 'gardening'])],
        ]);

        $this->browser()
            ->actingAs($requester)
            ->get('/api/area_requests?place=active')
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 1);

        $this->browser()
            ->actingAs($requester)
            ->get('/api/area_requests?activity=beehives')
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 1)
            ->assertJsonMatches('member[0].title', 'Ruches');
    }

    public function testPostCreatesDraftForCurrentUser(): void
    {
        $user = PersonFactory::createOne(['givenName' => 'Grace', 'familyName' => 'Hopper']);
        AreaActivityFactory::createOne(['code' => 'gardening']);

        $this->browser()
            ->actingAs($user)
            ->post('/api/area_requests', ['json' => [
                'title' => 'Je cherche un jardin',
                'description' => 'Pour cultiver des légumes',
                'minimalSurfaceRequested' => 20,
                'city' => 'Lyon',
                'activities' => ['gardening'],
            ]])
            ->assertStatus(201)
            ->assertJsonMatches('place', AreaRequestWorkflow::PLACE_DRAFT)
            ->assertJsonMatches('requesterFirstName', 'Grace')
            ->assertJsonMatches('activities', ['gardening']);

        AreaRequestFactory::assert()->count(1, ['requester' => $user]);
    }

    public function testPostRejectsInvalidPayload(): void
    {
        $this->browser()
            ->actingAs(PersonFactory::createOne())
            ->post('/api/area_requests', ['json' => [
                'title' => 'Je cherche un jardin',
                'description' => '',
                'minimalSurfaceRequested' => -1,
                'city' => 'Lyon',
            ]])
            ->assertStatus(422)
            ->assertJsonMatches("length(violations[?propertyPath=='description'])", 1)
            ->assertJsonMatches("length(violations[?propertyPath=='minimalSurfaceRequested'])", 1);

        AreaRequestFactory::assert()->empty();
    }

    public function testPatchOnlyUpdatesSentFieldsAndRejectsNull(): void
    {
        $requester = PersonFactory::createOne();
        $areaRequest = AreaRequestFactory::createOne([
            'requester' => $requester,
            'title' => 'Avant',
            'description' => 'Inchangée',
        ]);
        $uri = '/api/area_requests/'.$areaRequest->getUuid();

        $this->browser()
            ->actingAs($requester)
            ->patch($uri, ['json' => ['title' => 'Après']])
            ->assertStatus(200)
            ->assertJsonMatches('title', 'Après')
            ->assertJsonMatches('description', 'Inchangée');

        $this->browser()
            ->actingAs($requester)
            ->patch($uri, ['json' => ['description' => null]])
            ->assertStatus(422);
    }

    public function testDeleteOwnRequest(): void
    {
        $requester = PersonFactory::createOne();
        $areaRequest = AreaRequestFactory::createOne(['requester' => $requester]);

        $this->browser()
            ->actingAs($requester)
            ->delete('/api/area_requests/'.$areaRequest->getUuid())
            ->assertStatus(204);

        AreaRequestFactory::assert()->empty();
    }
}
