<?php

namespace App\Tests\Functional\Land;

use App\Repository\LandMemberInvitationRepository;
use App\Repository\LandMemberRepository;
use App\Security\Voter\LandMemberInvitationVoter;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use App\Workflow\LandMemberInvitation\LandMemberInvitationWorkflowPlace;
use App\Workflow\LandMemberInvitation\LandMemberInvitationWorkflowTransition;
use Zenstruck\Browser\Json;
use function Zenstruck\Foundry\faker;

class LandMemberInvitationTest extends AbstractApiTestCase
{
    public function testPost()
    {
        $context = $this->createLandContext();

        $email = faker()->email();

        // Owner
        $this->browser()->actingAs($context->owner)
            ->post('/api/land_member_invitations',
                ['json' => [
                    'email' => $email,
                    'land' => $this->getIriFromResource($context->land)
                ]])
            ->assertStatus(201)
            ->assertJsonMatches('email', $email)
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn(Json $json) => $json->isNotNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandMemberInvitationVoter::POST]);
        $this->addLandMember($context, [$landRole]);

        $email = faker()->email();

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->post('/api/land_member_invitations',
                ['json' => [
                    'email' => $email,
                    'land' => $this->getIriFromResource($context->land)
                ]])
            ->assertStatus(201)
            ->assertJsonMatches('email', $email)
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn(Json $json) => $json->isNotNull());
            });
    }

    public function testGet()
    {
        $context = $this->createLandContext();
        $this->addOneLandMemberInvitation($context);

        $landMemberInvitation = $context->landMemberInvitations[0];

        // Owner
        $this->browser()->actingAs($context->owner)
            ->get($this->getIriFromResource($landMemberInvitation))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landMemberInvitation->getUlid()->toString())
            ->assertJsonMatches('email', $landMemberInvitation->getEmail())
            ->assertJsonMatches('state', $landMemberInvitation->getState())
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandMemberInvitationVoter::GET]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($landMemberInvitation))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landMemberInvitation->getUlid()->toString())
            ->assertJsonMatches('email', $landMemberInvitation->getEmail())
            ->assertJsonMatches('state', $landMemberInvitation->getState())
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNull());
            });
    }

    public function testCollection()
    {
        $context = $this->createLandContext();
        $this->addOneLandMemberInvitation($context);
        $this->addOneLandMemberInvitation($context);

        // Owner
        $this->browser()->actingAs($context->owner)
            ->get('/api/land_member_invitations', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', count($context->landMemberInvitations))
            ->assertJsonMatches('member[0].ulid', $context->landMemberInvitations[0]->getUlid()->toString())
            ->assertJsonMatches('member[0].email', $context->landMemberInvitations[0]->getEmail())
            ->assertJsonMatches('member[0].state', $context->landMemberInvitations[0]->getState())
            ->assertJsonMatches('member[1].ulid', $context->landMemberInvitations[1]->getUlid()->toString())
            ->assertJsonMatches('member[1].email', $context->landMemberInvitations[1]->getEmail())
            ->assertJsonMatches('member[1].state', $context->landMemberInvitations[1]->getState());

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandMemberInvitationVoter::COLLECTION]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get('/api/land_member_invitations',
                ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', count($context->landMemberInvitations))
            ->assertJsonMatches('member[0].ulid', $context->landMemberInvitations[0]->getUlid()->toString())
            ->assertJsonMatches('member[0].email', $context->landMemberInvitations[0]->getEmail())
            ->assertJsonMatches('member[0].state', $context->landMemberInvitations[0]->getState())
            ->assertJsonMatches('member[1].ulid', $context->landMemberInvitations[1]->getUlid()->toString())
            ->assertJsonMatches('member[1].email', $context->landMemberInvitations[1]->getEmail())
            ->assertJsonMatches('member[1].state', $context->landMemberInvitations[1]->getState());
    }

    public function testCollectionPagination()
    {
        $context = $this->createLandContext();
        array_map(fn() => $this->addOneLandMemberInvitation($context), range(1, 25));

        $this->browser()->actingAs($context->owner)
            ->get('/api/land_member_invitations',
                ['query' => ['land' => $context->land->getUlid()->toString(), 'itemsPerPage' => 10, 'page' => 2]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 25)
            ->use(function (Json $json) {
                $json->assertThat('member', fn(Json $json) => $json->hasCount(10));
            });
    }

    public function testDelete()
    {
        $context = $this->createLandContext();
        $this->addOneLandMemberInvitation($context);

        // Owner
        $this->browser()->actingAs($context->owner)
            ->delete($this->getIriFromResource($context->landMemberInvitations[0]))
            ->assertStatus(204);

        // Member with permissions
        $this->addOneLandMemberInvitation($context);
        $landRole = $this->createLandRole($context->land, [LandMemberInvitationVoter::DELETE]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->delete($this->getIriFromResource($context->landMemberInvitations[1]))
            ->assertStatus(204);
    }

    public function testAccept()
    {
        $context = $this->createLandContext();
        $invited = $this->createPerson();
        $this->addOneLandRole($context);
        $this->addOneLandMemberInvitation($context, [$context->landRoles[0]], $invited->getEmail());

        $this->browser()->actingAs($invited)
            ->patch(
                $this->getIriFromResource($context->landMemberInvitations[0]) . '/' . LandMemberInvitationWorkflowTransition::ACCEPT,
                ['json' => []])
            ->assertSuccessful();

        $landMemberRepository = static::getContainer()->get(LandMemberRepository::class);
        $landMember = $landMemberRepository->findOneBy(['person' => $invited,
                                                        'land' => $context->land]);

        $this->assertNotNull($landMember);
        $roleUlids = static fn (array $roles): array => array_map(
            static fn ($role): string => $role->getUlid()->toString(),
            $roles
        );
        $this->assertEqualsCanonicalizing(
            $roleUlids($landMember->getLandRoles()->toArray()),
            $roleUlids($context->landMemberInvitations[0]->getLandRoles()->toArray())
        );

        $landMemberInvitationRepository = static::getContainer()->get(LandMemberInvitationRepository::class);
        $landMemberInvitation = $landMemberInvitationRepository->findOneBy(['person' => $invited,
                                                                            'land' => $context->land,
                                                                            'state' => LandMemberInvitationWorkflowPlace::ACCEPTED]);

        $this->assertNotNull($landMemberInvitation);
        $this->assertNotNull($landMemberInvitation->getAcceptedAt());
        $this->assertNull($landMemberInvitation->getRefusedAt());
    }

    public function testAutoLink()
    {
        $context = $this->createLandContext();
        $invited = $this->createPerson();
        $this->addOneLandRole($context);
        $this->addOneLandMemberInvitation($context, [$context->landRoles[0]], $invited->getEmail());

        $landMemberInvitationRepository = static::getContainer()->get(LandMemberInvitationRepository::class);
        $landMemberInvitation = $landMemberInvitationRepository->findOneBy(['person' => $invited,
                                                                            'land' => $context->land,
                                                                            'state' => LandMemberInvitationWorkflowPlace::PENDING]);

        $this->assertNotNull($landMemberInvitation);

        $email = faker()->email();
        $this->addOneLandMemberInvitation($context, [$context->landRoles[0]], $email);

        $landMemberInvitation = $landMemberInvitationRepository->findOneBy(['land' => $context->land,
                                                                            'state' => LandMemberInvitationWorkflowPlace::PENDING,
                                                                            'person' => null]);
        $this->assertNotNull($landMemberInvitation);

        $invited = $this->createPerson(['email' => $email]);
        $landMemberInvitation = $landMemberInvitationRepository->findOneBy(['land' => $context->land,
                                                                            'state' => LandMemberInvitationWorkflowPlace::PENDING,
                                                                            'person' => $invited]);
        $this->assertNotNull($landMemberInvitation);
    }

    public function testRefuse()
    {
        $context = $this->createLandContext();
        $invited = $this->createPerson();
        $this->addOneLandMemberInvitation($context, null, $invited->getEmail());

        $this->browser()->actingAs($invited)
            ->patch(
                $this->getIriFromResource($context->landMemberInvitations[0]) . '/' . LandMemberInvitationWorkflowTransition::REFUSE,
                ['json' => []])
            ->assertSuccessful();

        $landMemberRepository = static::getContainer()->get(LandMemberRepository::class);
        $landMember = $landMemberRepository->findOneBy(['person' => $invited,
                                                        'land' => $context->land]);

        $this->assertNull($landMember);

        $landMemberInvitationRepository = static::getContainer()->get(LandMemberInvitationRepository::class);
        $landMemberInvitation = $landMemberInvitationRepository->findOneBy(['id' => $context->landMemberInvitations[0]->getId(),
                                                                            'land' => $context->land,
                                                                            'state' => LandMemberInvitationWorkflowPlace::REFUSED]);

        $this->assertNotNull($landMemberInvitation);
        $this->assertNotNull($landMemberInvitation->getRefusedAt());
        $this->assertNull($landMemberInvitation->getAcceptedAt());
    }

    public function testCheckUnicity()
    {
        $context = $this->createLandContext();
        $email = faker()->email();
        $this->addOneLandMemberInvitation($context, null, $email);
        $this->browser()->actingAs($context->owner)
            ->get('/api/land_member_invitations/check_email_unicity',
                ['query'
                 => [
                        'email' => $email,
                        'land' => $context->land->getUlid()->toString()
                    ]])
            ->assertSuccessful()
            ->assertJsonMatches('isUnique', false);
    }

    public function testCollectionByEmail()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $context3 = $this->createLandContext();
        $this->addOneLandRole($context1);
        $this->addOneLandRole($context3);
        $this->addOneLandMemberInvitation($context1, [$context1->landRoles[0]], $context2->owner->getEmail());
        $this->addOneLandMemberInvitation($context3, [$context3->landRoles[0]], $context2->owner->getEmail());

        $this->browser()->actingAs($context2->owner)
            ->get('/api/land_member_invitations/by_email', ['query' => ['email' => $context2->owner->getEmail()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 2)
            ->assertJsonMatches('member[0].ulid', $context1->landMemberInvitations[0]->getUlid()->toString())
            ->assertJsonMatches('member[0].land.name', $context1->landMemberInvitations[0]->getLand()->getName())
            ->use(function (Json $json) {
                $json->assertThat('member[0].createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('member[0].landRoles', fn(Json $json) => $json->hasCount(1));
            })
            ->assertJsonMatches('member[0].landRoles[0].name', $context1->landRoles[0]->getName());
    }
}
