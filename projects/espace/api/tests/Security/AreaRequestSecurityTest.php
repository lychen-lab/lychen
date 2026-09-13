<?php

namespace App\Tests\Security;

use App\Factory\AreaRequestFactory;
use App\Factory\PersonFactory;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use App\Workflow\AreaRequest\AreaRequestWorkflow;

class AreaRequestSecurityTest extends AbstractApiTestCase
{
    public function testAnonymousUserIsDenied(): void
    {
        $this->browser()
            ->get('/api/area_requests')
            ->assertStatus(401);
    }

    public function testCollectionListsActiveRequestsAndOwnOnes(): void
    {
        $user = PersonFactory::createOne();
        $other = PersonFactory::createOne();
        AreaRequestFactory::createOne(['requester' => $user, 'place' => AreaRequestWorkflow::PLACE_DRAFT]);
        AreaRequestFactory::createOne(['requester' => $other, 'place' => AreaRequestWorkflow::PLACE_ACTIVE]);
        AreaRequestFactory::createOne(['requester' => $other, 'place' => AreaRequestWorkflow::PLACE_DRAFT]);
        AreaRequestFactory::createOne(['requester' => $other, 'place' => AreaRequestWorkflow::PLACE_PENDING_VALIDATION]);
        AreaRequestFactory::createOne(['requester' => $other, 'place' => AreaRequestWorkflow::PLACE_REJECTED]);

        $this->browser()
            ->actingAs($user)
            ->get('/api/area_requests')
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 2);
    }

    public function testInactiveRequestOfAnotherUserIsNotReachable(): void
    {
        $user = PersonFactory::createOne();
        $uri = '/api/area_requests/'.AreaRequestFactory::createOne([
            'requester' => PersonFactory::createOne(),
            'place' => AreaRequestWorkflow::PLACE_PENDING_VALIDATION,
        ])->getUuid();

        $this->browser()->actingAs($user)->get($uri)->assertStatus(404);
        $this->browser()->actingAs($user)->patch($uri, ['json' => ['title' => 'Piraté']])->assertStatus(404);
        $this->browser()->actingAs($user)->delete($uri)->assertStatus(404);
    }

    public function testActiveRequestOfAnotherUserIsReadOnly(): void
    {
        $user = PersonFactory::createOne();
        $uri = '/api/area_requests/'.AreaRequestFactory::createOne([
            'requester' => PersonFactory::createOne(),
            'place' => AreaRequestWorkflow::PLACE_ACTIVE,
        ])->getUuid();

        $this->browser()->actingAs($user)->get($uri)->assertStatus(200);
        $this->browser()->actingAs($user)->patch($uri, ['json' => ['title' => 'Piraté']])->assertStatus(404);
        $this->browser()->actingAs($user)->delete($uri)->assertStatus(404);
    }

    public function testRequesterCanManageOwnDraft(): void
    {
        $requester = PersonFactory::createOne();
        $uri = '/api/area_requests/'.AreaRequestFactory::createOne([
            'requester' => $requester,
            'place' => AreaRequestWorkflow::PLACE_DRAFT,
        ])->getUuid();

        $this->browser()->actingAs($requester)->get($uri)->assertStatus(200);
        $this->browser()->actingAs($requester)->patch($uri, ['json' => ['title' => 'Mis à jour']])->assertStatus(200);
        $this->browser()->actingAs($requester)->delete($uri)->assertStatus(204);
    }
}
