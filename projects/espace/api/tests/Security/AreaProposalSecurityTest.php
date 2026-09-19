<?php

namespace App\Tests\Security;

use App\Factory\AreaProposalFactory;
use App\Factory\PersonFactory;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use App\Workflow\AreaProposal\AreaProposalWorkflow;

class AreaProposalSecurityTest extends AbstractApiTestCase
{
    public function testAnonymousUserIsDenied(): void
    {
        $this->browser()
            ->get('/api/area_proposals')
            ->assertStatus(401);
    }

    public function testCollectionListsPublishedProposalsAndOwnOnes(): void
    {
        $user = PersonFactory::createOne();
        $other = PersonFactory::createOne();
        AreaProposalFactory::createOne(['proposer' => $user, 'place' => AreaProposalWorkflow::PLACE_DRAFT]);
        AreaProposalFactory::createOne(['proposer' => $other, 'place' => AreaProposalWorkflow::PLACE_PUBLISHED]);
        AreaProposalFactory::createOne(['proposer' => $other, 'place' => AreaProposalWorkflow::PLACE_DRAFT]);
        AreaProposalFactory::createOne(['proposer' => $other, 'place' => AreaProposalWorkflow::PLACE_VERIFICATION]);
        AreaProposalFactory::createOne(['proposer' => $other, 'place' => AreaProposalWorkflow::PLACE_ARCHIVED]);

        $this->browser()
            ->actingAs($user)
            ->get('/api/area_proposals')
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 2);
    }

    public function testUnpublishedProposalOfAnotherUserIsNotReachable(): void
    {
        $user = PersonFactory::createOne();
        $uri = '/api/area_proposals/'.AreaProposalFactory::createOne([
            'proposer' => PersonFactory::createOne(),
            'place' => AreaProposalWorkflow::PLACE_DRAFT,
        ])->getUuid();

        $this->browser()->actingAs($user)->get($uri)->assertStatus(404);
        $this->browser()->actingAs($user)->patch($uri, ['json' => ['title' => 'Piraté']])->assertStatus(404);
        $this->browser()->actingAs($user)->delete($uri)->assertStatus(404);
    }

    public function testPublishedProposalOfAnotherUserIsReadOnly(): void
    {
        $user = PersonFactory::createOne();
        $uri = '/api/area_proposals/'.AreaProposalFactory::createOne([
            'proposer' => PersonFactory::createOne(),
            'place' => AreaProposalWorkflow::PLACE_PUBLISHED,
        ])->getUuid();

        $this->browser()->actingAs($user)->get($uri)->assertStatus(200);
        $this->browser()->actingAs($user)->patch($uri, ['json' => ['title' => 'Piraté']])->assertStatus(404);
        $this->browser()->actingAs($user)->delete($uri)->assertStatus(404);
    }

    public function testProposerCanManageOwnDraft(): void
    {
        $proposer = PersonFactory::createOne();
        $uri = '/api/area_proposals/'.AreaProposalFactory::createOne([
            'proposer' => $proposer,
            'place' => AreaProposalWorkflow::PLACE_DRAFT,
        ])->getUuid();

        $this->browser()->actingAs($proposer)->get($uri)->assertStatus(200);
        $this->browser()->actingAs($proposer)->patch($uri, ['json' => ['title' => 'Mis à jour']])->assertStatus(200);
        $this->browser()->actingAs($proposer)->delete($uri)->assertStatus(204);
    }
}
