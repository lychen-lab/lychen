<?php

namespace App\Tests\Security;

use App\Entity\Land;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use App\Workflow\LandMemberInvitation\LandMemberInvitationWorkflowTransition;
use Exception;
use function Zenstruck\Foundry\faker;

class LandMemberInvitationSecurityTest extends AbstractApiTestCase
{
    public function testPut()
    {
        $context = $this->createLandContext();
        $this->addOneLandMemberInvitation($context);

        $this->browser()
            ->put($this->getIriFromResource($context->landMemberInvitations[0]))
            ->assertStatus(405);

        // Does not exist
        $this->browser()->actingAs($context->owner)
            ->put($this->getIriFromResource($context->landMemberInvitations[0]))
            ->assertStatus(405);
    }

    public function testPost()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();

        // User cannot create a LandMemberInvitation if they are not authenticated
        $this->browser()
            ->post('/api/land_member_invitations', ['json' => ['land' => $this->getIriFromResource($context1->land)]])
            ->assertStatus(401);

        // User cannot create a LandMemberInvitation for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->post('/api/land_member_invitations', ['json' => ['land' => $this->getIriFromResource($context1->land)]])
            ->assertStatus(403);

        // User cannot create a LandMemberInvitation for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->post('/api/land_member_invitations',
                ['json' => ['land' => $this->getIriFromResource($context1->land)]])
            ->assertStatus(403);
    }

    public function testPatch()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addOneLandMemberInvitation($context1);

        // User cannot patch a LandMemberInvitation if they are not authenticated
        $this->browser()
            ->patch($this->getIriFromResource($context1->landMemberInvitations[0]), ['json' => []])
            ->assertStatus(401);

        // User cannot patch a LandMemberInvitation for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->patch($this->getIriFromResource($context1->landMemberInvitations[0]), ['json' => []])
            ->assertStatus(403);

        // User cannot patch a LandMemberInvitation with a Land they are not a member of (land property should be ignored)
        $this->browser()->actingAs($context1->owner)
            ->patch($this->getIriFromResource($context1->landMemberInvitations[0]),
                ['json' => ['land' => $this->getIriFromResource($context2->land)]])
            ->assertSuccessful();

        $this->browser()->actingAs($context1->owner)
            ->get($this->getIriFromResource($context1->landMemberInvitations[0]))
            ->assertSuccessful()
            ->assertJsonMatches('land', $this->findIriBy(Land::class, ['id' => $context1->land->getId()]));

        $this->browser()->actingAs($context2->owner)
            ->get($this->getIriFromResource($context1->landMemberInvitations[0]))
            ->assertStatus(403);

        // User cannot patch a LandMemberInvitation for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($context1->landMemberInvitations[0]), ['json' => []])
            ->assertStatus(403);
    }

    public function testAccept()
    {
        $context1 = $this->createLandContext();
        $this->addOneLandMemberInvitation($context1);

        $uri = $this->getIriFromResource($context1->landMemberInvitations[0]) . '/' . LandMemberInvitationWorkflowTransition::ACCEPT;

        // User cannot accept a LandMemberInvitation if they are not authenticated
        $this->browser()
            ->patch($uri, ['json' => []])
            ->assertStatus(401);

        // User cannot accept a LandMemberInvitation even if they are owner
        $this->browser()->actingAs($context1->owner)
            ->patch($uri, ['json' => []])
            ->assertStatus(403);

        // User cannot accept a LandMemberInvitation
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->patch($uri, ['json' => []])
            ->assertStatus(403);
    }

    public function testRefuse()
    {
        $context1 = $this->createLandContext();
        $this->addOneLandMemberInvitation($context1);
        $uri = $this->getIriFromResource($context1->landMemberInvitations[0]) . '/' . LandMemberInvitationWorkflowTransition::REFUSE;

        // User cannot refuse a LandMemberInvitation if they are not authenticated
        $this->browser()
            ->patch($uri, ['json' => []])
            ->assertStatus(401);

        // User cannot refuse a LandMemberInvitation even if they are owner
        $this->browser()->actingAs($context1->owner)
            ->patch($uri, ['json' => []])
            ->assertStatus(403);

        // User cannot refuse a LandMemberInvitation
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->patch($uri, ['json' => []])
            ->assertStatus(403);
    }

    public function testGet()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addOneLandMemberInvitation($context1);

        // User cannot get a LandMemberInvitation if they are not authenticated
        $this->browser()
            ->get($this->getIriFromResource($context1->landMemberInvitations[0]))
            ->assertStatus(401);

        // User cannot get a LandMemberInvitation for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->get($this->getIriFromResource($context1->landMemberInvitations[0]))
            ->assertStatus(403);

        // User cannot get a LandMemberInvitation for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($context1->landMemberInvitations[0]))
            ->assertStatus(403);
    }

    public function testDelete()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addOneLandMemberInvitation($context1);

        // User cannot delete a Land if they are not authenticated
        $this->browser()
            ->delete($this->getIriFromResource($context1->landMemberInvitations[0]))
            ->assertStatus(401);

        // User cannot delete a LandMemberInvitation for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->delete($this->getIriFromResource($context1->landMemberInvitations[0]))
            ->assertStatus(403);

        // User cannot delete a LandMemberInvitation for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->delete($this->getIriFromResource($context1->landMemberInvitations[0]))
            ->assertStatus(403);
    }

    public function testCollection()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addOneLandMemberInvitation($context1);

        // User cannot list LandMemberInvitation if they are not authenticated
        $this->browser()
            ->get('/api/land_member_invitations', ['query' => ['land' => $context1->land->getUlid()->toString()]])
            ->assertStatus(401);

        // User cannot list LandMemberInvitation without a Land query parameter
        $this->browser()->actingAs($context1->owner)
            ->get('/api/land_member_invitations', ['query' => ['land' => '']])
            ->assertStatus(400);

        // User cannot list LandMemberInvitation for a Land they are not a member of
        $this->browser()->actingAs($context2->owner)
            ->get('/api/land_member_invitations',
                ['query' => ['land' => $context1->land->getUlid()->toString()]])
            ->assertStatus(403);

        // User cannot list LandMemberInvitation for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->get('/api/land_member_invitations',
                ['query' => ['land' => $context1->land->getUlid()->toString()]])
            ->assertStatus(403);
    }

    public function testCheckUnicity()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $email = faker()->email();
        $this->addOneLandMemberInvitation($context1, null, $email);
        $this->browser()->actingAs($context2->owner)
            ->get('/api/land_member_invitations/check_email_unicity',
                ['query'
                 => [
                        'email' => $email,
                        'land' => $context1->land->getUlid()->toString()
                    ]])
            ->assertStatus(403);

        // User cannot check email unicity for a Land for which they do not have permission
        $landRole = $this->createLandRole($context1->land);
        $this->addLandMember($context1, [$landRole]);
        $this->browser()->actingAs($context1->landMembers[0]->getPerson())
            ->get('/api/land_member_invitations/check_email_unicity',
                ['query'
                 => [
                        'email' => $email,
                        'land' => $context1->land->getUlid()->toString()
                    ]])
            ->assertStatus(403);
    }

    public function testCollectionByEmail()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addOneLandRole($context1);
        $this->addOneLandMemberInvitation($context1, [$context1->landRoles[0]], $context2->owner->getEmail());

        $this->browser()->actingAs($context1->owner)
            ->get('/api/land_member_invitations/by_email', ['query' => ['email' => $context2->owner->getEmail()]])
            ->assertStatus(403);
    }

    public function testCantAddRolesFromAnotherLand()
    {
        $this->expectException(Exception::class);

        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addOneLandRole($context1);
        $this->addOneLandRole($context2);
        $this->addOneLandMemberInvitation($context1, [$context2->landRoles[0]], $context2->owner->getEmail());
    }
}
