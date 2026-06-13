<?php

namespace App\Tests\Functional\Land;

use App\Repository\LandMemberRepository;
use App\Security\Constant\LandMemberPermission;
use App\Security\Voter\LandMemberVoter;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use Zenstruck\Browser\Json;

class LandMemberTest extends AbstractApiTestCase
{
    public function testGet()
    {
        $context = $this->createLandContext();
        $this->addLandMember($context);

        $landMember = $context->landMembers[0];

        // Owner
        $this->browser()->actingAs($context->owner)
            ->get($this->getIriFromResource($landMember))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landMember->getUlid()->toString())
            ->assertJsonMatches('owner', $landMember->isOwner())
            ->use(function (Json $json) {
                $json->assertThat('landMemberSetting', fn(Json $json) => $json->isNotNull());
                $json->assertThat('landRoles', fn(Json $json) => $json->isNotNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandMemberVoter::GET]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[1]->getPerson())
            ->get($this->getIriFromResource($landMember))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landMember->getUlid()->toString())
            ->assertJsonMatches('owner', $landMember->isOwner())
            ->use(function (Json $json) {
                $json->assertThat('landMemberSetting', fn(Json $json) => $json->isNotNull());
                $json->assertThat('landRoles', fn(Json $json) => $json->isNotNull());
            });

        // Member without permissions but he is the guy of the LandMember
        $landRole = $this->createLandRole($context->land, []);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[2]->getPerson())
            ->get($this->getIriFromResource($context->landMembers[2]))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $context->landMembers[2]->getUlid()->toString())
            ->assertJsonMatches('owner', $context->landMembers[2]->isOwner())
            ->use(function (Json $json) {
                $json->assertThat('landMemberSetting', fn(Json $json) => $json->isNotNull());
                $json->assertThat('landRoles', fn(Json $json) => $json->isNotNull());
            });
    }

    public function testPatch()
    {
        $context = $this->createLandContext();
        $this->addLandMember($context);

        $landMember = $context->landMembers[0];

        $newRoles = [];

        $this->browser()->actingAs($context->owner)
            ->patch($this->getIriFromResource($landMember),
                [
                    'json' => [
                        'landRoles' => $newRoles
                    ]
                ])
            ->assertStatus(200)
            ->assertJsonMatches('ulid', $landMember->getUlid()->toString())
            ->use(function (Json $json) {
                $json->assertThat('landRoles', fn(Json $json) => $json->isNotNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandMemberVoter::PATCH]);
        $this->addLandMember($context, [$landRole]);

        $newRoles = [];

        $this->browser()->actingAs($context->landMembers[1]->getPerson())
            ->patch($this->getIriFromResource($landMember),
                [
                    'json' => [
                        'landRoles' => $newRoles
                    ]
                ])
            ->assertStatus(200)
            ->assertJsonMatches('ulid', $landMember->getUlid()->toString())
            ->use(function (Json $json) {
                $json->assertThat('landRoles', fn(Json $json) => $json->isNotNull());
            });
    }

    public function testCollection()
    {
        $context = $this->createLandContext();
        $this->addLandMember($context);
        $this->addLandMember($context);

        // Owner
        $this->browser()->actingAs($context->owner)
            ->get('/api/land_members', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', count($context->landMembers) + 1);

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandMemberVoter::COLLECTION]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[2]->getPerson())
            ->get('/api/land_members', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', count($context->landMembers) + 1);

        // API Key
        $landApiKey = $this->createLandApiKey($context->land, ['permissions' => [LandMemberVoter::COLLECTION]]);
        $this->browser()->actingAs($landApiKey)
            ->get('/api/land_members', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful();
    }

    public function testCollectionPagination()
    {
        $context = $this->createLandContext();
        array_map(fn() => $this->addLandMember($context), range(1, 25));

        $this->browser()->actingAs($context->owner)
            ->get('/api/land_members',
                ['query' => ['land' => $context->land->getUlid()->toString(),
                             'itemsPerPage' => 10,
                             'page' => 2]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 26)
            ->use(function (Json $json) {
                $json->assertThat('member', fn(Json $json) => $json->hasCount(10));
            });
    }

    public function testDelete()
    {
        $context = $this->createLandContext();
        $context = $this->addLandMember($context);

        // Owner
        $this->browser()->actingAs($context->owner)
            ->delete($this->getIriFromResource($context->landMembers[0]))
            ->assertStatus(204);

        // Member with permissions
        $this->addLandMember($context);
        $landRole = $this->createLandRole($context->land, [LandMemberVoter::DELETE]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[2]->getPerson())
            ->delete($this->getIriFromResource($context->landMembers[1]))
            ->assertStatus(204);

        // Member without permissions but he is the guy of the LandMember
        $landRole = $this->createLandRole($context->land, []);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[3]->getPerson())
            ->delete($this->getIriFromResource($context->landMembers[3]))
            ->assertStatus(204);

        $landRole = $this->createLandRole($context->land, []);
        $this->addLandMember($context, [$landRole]);

        // API Key
        $landApiKey = $this->createLandApiKey($context->land, ['permissions' => [LandMemberVoter::DELETE]]);
        $this->browser()->actingAs($landApiKey)
            ->delete($this->getIriFromResource($context->landMembers[4]))
            ->assertStatus(204);
    }

    public function testCantDeleteOwner()
    {
        $context = $this->createLandContext();

        $landMemberRepository = static::getContainer()->get(LandMemberRepository::class);
        $landMember = $landMemberRepository->findOneBy(['person' => $context->owner,
                                                        'land' => $context->land]);

        $this->browser()->actingAs($context->owner)
            ->delete($this->getIriFromResource($landMember))
            ->assertStatus(403);

        // API Key
        $landApiKey = $this->createLandApiKey($context->land, ['permissions' => [LandMemberVoter::DELETE]]);
        $this->browser()->actingAs($landApiKey)
            ->delete($this->getIriFromResource($landMember))
            ->assertStatus(403);
    }

    public function testMe()
    {
        $context = $this->createLandContext();
        $context2 = $this->createLandContext();
        $this->addOneLandRole($context2, LandMemberPermission::ALL);
        $this->addLandMember($context2, [$context2->landRoles[0]], $context->owner);

        $landMemberRepository = static::getContainer()->get(LandMemberRepository::class);
        $landMember = $landMemberRepository->findOneBy(['person' => $context->owner,
                                                        'land' => $context->land]);

        $this->browser()->actingAs($context->owner)
            ->get('/api/land_members/me', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landMember->getUlid()->toString())
            ->assertJsonMatches('owner', $landMember->isOwner())
            ->assertJsonMatches('land', $this->getIriFromResource($context->land))
            ->use(function (Json $json) {
                $json->assertThat('landRoles', fn(Json $json) => $json->hasCount(0));
            });

        $this->addOneLandRole($context, LandMemberPermission::ALL);
        $this->addLandMember($context, [$context->landRoles[0]]);
        $landMember = $context->landMembers[0];

        $this->browser()->actingAs($landMember->getPerson())
            ->get('/api/land_members/me', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landMember->getUlid()->toString())
            ->assertJsonMatches('owner', $landMember->isOwner())
            ->assertJsonMatches('land', $this->getIriFromResource($context->land))
            ->use(function (Json $json) {
                $json->assertThat('landRoles', fn(Json $json) => $json->hasCount(1));
                $json->assertThat('landRoles[0].permissions', fn(Json $json) => $json->isNotNull());
            })
            ->assertJsonMatches('landRoles[0].name', $context->landRoles[0]->getName());

        // API Key
        $personApiKey = $this->createPersonApiKey($context->owner, ['permissions' => [LandMemberVoter::ME]]);
        $this->browser()->actingAs($personApiKey)
            ->get('/api/land_members/me', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertStatus(200);
    }
}
