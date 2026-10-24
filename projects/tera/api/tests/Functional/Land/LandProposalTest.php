<?php

namespace App\Tests\Functional\Land;

use App\Constant\GardeningLevel;
use App\Constant\LandInteractionMode;
use App\Constant\LandSharingCondition;
use App\Constant\Orientation;
use App\Constant\SoilType;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use App\Workflow\LandProposal\LandProposalWorkflowPlace;
use App\Workflow\LandProposal\LandProposalWorkflowTransition;
use Zenstruck\Browser\Json;

class LandProposalTest extends AbstractApiTestCase
{
    public function testPost()
    {
        $context = $this->createLandContext();

        $data = [
            'title' => 'My Awesome Garden Proposal',
            'description' => ['type' => 'doc',
                'content' => [['type' => 'paragraph',
                    'content' => [['type' => 'text',
                        'text' => 'Detailed description here.']]]]],
            // Example Tiptap JSON
            'soilType' => SoilType::LOAMY,
            'orientation' => Orientation::SOUTH,
            'hasParking' => true,
            'hasTools' => true,
            'hasShed' => false,
            'hasWaterPoint' => true,
            'hasIndependentAccess' => true,
            'gardenState' => 'Well maintained',
            // TODO: Use constant when created
            'preferredInteractionMode' => LandInteractionMode::TOGETHER_BUT_NOT_ALL_TIME,
            'gardeningLevel' => GardeningLevel::BEGINNER,
            'lookingForGardenerLevel' => GardeningLevel::ADVANCED,
            'gardenTotalSurface' => 150,
            'foodSecurityParticipation' => true,
            'sharingConditions' => [LandSharingCondition::GARDENING, LandSharingCondition::VEGETABLE_SHARING],
            'land' => $this->getIriFromResource($context->land),
        ];

        $this->browser()->actingAs($context->owner)
            ->post('/api/land_proposals', ['json' => $data])
            ->assertStatus(201)
            ->assertJsonMatches('title', $data['title'])
            ->assertJsonMatches('description', $data['description'])
            ->assertJsonMatches('soilType', $data['soilType'])
            ->assertJsonMatches('orientation', $data['orientation'])
            ->assertJsonMatches('hasParking', $data['hasParking'])
            ->assertJsonMatches('hasTools', $data['hasTools'])
            ->assertJsonMatches('hasShed', $data['hasShed'])
            ->assertJsonMatches('hasWaterPoint', $data['hasWaterPoint'])
            ->assertJsonMatches('hasIndependentAccess', $data['hasIndependentAccess'])
            ->assertJsonMatches('gardenState', $data['gardenState'])
            ->assertJsonMatches('preferredInteractionMode', $data['preferredInteractionMode'])
            ->assertJsonMatches('gardeningLevel', $data['gardeningLevel'])
            ->assertJsonMatches('lookingForGardenerLevel', $data['lookingForGardenerLevel'])
            ->assertJsonMatches('gardenTotalSurface', $data['gardenTotalSurface'])
            ->assertJsonMatches('foodSecurityParticipation', $data['foodSecurityParticipation'])
            ->assertJsonMatches('sharingConditions', $data['sharingConditions'])
            ->assertJsonMatches('state', LandProposalWorkflowPlace::DRAFT) // Default state
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn (Json $json) => $json->isNotNull());
                $json->assertThat('createdAt', fn (Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn (Json $json) => $json->isNull());
                $json->assertThat('publishedAt', fn (Json $json) => $json->isNull());
                $json->assertThat('archivedAt', fn (Json $json) => $json->isNull());
            });
    }

    public function testPatch(): void
    {
        $context = $this->createLandContext();
        $landProposal = $this->createLandProposal($context->land);

        $data = [
            'title' => 'Updated Garden Proposal',
            'description' => ['type' => 'doc',
                'content' => [['type' => 'paragraph',
                    'content' => [['type' => 'text', 'text' => 'Updated description.']]]]],
            'soilType' => SoilType::SANDY,
            'orientation' => Orientation::NORTH,
            'hasParking' => false,
            'hasTools' => false,
            'hasShed' => true,
            'hasWaterPoint' => false,
            'hasIndependentAccess' => false,
            'gardenState' => 'Needs some work',
            'preferredInteractionMode' => LandInteractionMode::ALONE,
            'gardeningLevel' => GardeningLevel::ADVANCED,
            'lookingForGardenerLevel' => GardeningLevel::BEGINNER,
            'gardenTotalSurface' => 50,
            'foodSecurityParticipation' => false,
            'sharingConditions' => [LandSharingCondition::GARDENING],
        ];

        $this->browser()->actingAs($context->owner)
            ->patch($this->getIriFromResource($landProposal), ['json' => $data])
            ->assertStatus(200)
            ->assertJsonMatches('title', $data['title'])
            ->assertJsonMatches('description', $data['description'])
            ->assertJsonMatches('soilType', $data['soilType'])
            ->assertJsonMatches('orientation', $data['orientation'])
            ->assertJsonMatches('hasParking', $data['hasParking'])
            ->assertJsonMatches('hasTools', $data['hasTools'])
            ->assertJsonMatches('hasShed', $data['hasShed'])
            ->assertJsonMatches('hasWaterPoint', $data['hasWaterPoint'])
            ->assertJsonMatches('hasIndependentAccess', $data['hasIndependentAccess'])
            ->assertJsonMatches('gardenState', $data['gardenState'])
            ->assertJsonMatches('preferredInteractionMode', $data['preferredInteractionMode'])
            ->assertJsonMatches('gardeningLevel', $data['gardeningLevel'])
            ->assertJsonMatches('lookingForGardenerLevel', $data['lookingForGardenerLevel'])
            ->assertJsonMatches('gardenTotalSurface', $data['gardenTotalSurface'])
            ->assertJsonMatches('foodSecurityParticipation', $data['foodSecurityParticipation'])
            ->assertJsonMatches('sharingConditions', $data['sharingConditions'])
            ->use(function (Json $json) {
                $json->assertThat('updatedAt', fn (Json $json) => $json->isNotNull());
            });
    }

    public function testGet(): void
    {
        $context = $this->createLandContext();
        $landProposal = $this->createLandProposal($context->land,
            [
                'state' => LandProposalWorkflowPlace::PUBLISHED, // Make it published for public access test
                'title' => 'Test Get Proposal',
                'description' => ['type' => 'doc',
                    'content' => [['type' => 'paragraph',
                        'content' => [['type' => 'text', 'text' => 'Get description.']]]]],
                'soilType' => SoilType::SANDY,
                'orientation' => Orientation::NORTH,
                'hasParking' => false,
                'hasTools' => false,
                'hasShed' => true,
                'hasWaterPoint' => false,
                'hasIndependentAccess' => false,
                'gardenState' => 'Needs some work',
                'preferredInteractionMode' => LandInteractionMode::ALONE,
                'gardeningLevel' => GardeningLevel::ADVANCED,
                'lookingForGardenerLevel' => GardeningLevel::BEGINNER,
                'gardenTotalSurface' => 50,
                'foodSecurityParticipation' => false,
                'sharingConditions' => [LandSharingCondition::GARDENING],
            ]);

        // --- Test Get as Owner ---
        $this->browser()->actingAs($context->owner)
            ->get($this->getIriFromResource($landProposal))
            ->assertStatus(200)
            ->assertJsonMatches('ulid', $landProposal->getUlid()->toString())
            ->assertJsonMatches('title', $landProposal->getTitle())
            ->assertJsonMatches('description', $landProposal->getDescription())
            ->assertJsonMatches('soilType', $landProposal->getSoilType())
            ->assertJsonMatches('orientation', $landProposal->getOrientation())
            ->assertJsonMatches('hasParking', $landProposal->getHasParking())
            ->assertJsonMatches('hasTools', $landProposal->getHasTools())
            ->assertJsonMatches('hasShed', $landProposal->getHasShed())
            ->assertJsonMatches('hasWaterPoint', $landProposal->getHasWaterPoint())
            ->assertJsonMatches('hasIndependentAccess', $landProposal->getHasIndependentAccess())
            ->assertJsonMatches('gardenState', $landProposal->getGardenState())
            ->assertJsonMatches('preferredInteractionMode', $landProposal->getPreferredInteractionMode())
            ->assertJsonMatches('gardeningLevel', $landProposal->getGardeningLevel())
            ->assertJsonMatches('lookingForGardenerLevel', $landProposal->getLookingForGardenerLevel())
            ->assertJsonMatches('gardenTotalSurface', $landProposal->getGardenTotalSurface())
            ->assertJsonMatches('foodSecurityParticipation', $landProposal->getFoodSecurityParticipation())
            ->assertJsonMatches('sharingConditions', $landProposal->getSharingConditions())
            ->assertJsonMatches('state', $landProposal->getState());
    }

    public function testGetFromAnotherPerson(): void
    {
        $context = $this->createLandContext();
        $landProposal = $this->createLandProposal($context->land, ['state' => LandProposalWorkflowPlace::PUBLISHED]);
        // --- Test Get as another authenticated user (should work for published proposals) ---
        $otherUser = $this->createPerson();

        $this->browser()->actingAs($otherUser)
            ->get($this->getIriFromResource($landProposal))
            ->assertStatus(200) // Assuming published proposals are readable by any authenticated user based on Voter logic
            ->assertJsonMatches('title', $landProposal->getTitle()); // Check at least one field
    }

    public function testCollection()
    {
        $context = $this->createLandContext();
        $context2 = $this->createLandContext();

        // Create proposals with specific states for filtering tests
        $landProposal1 = $this->createLandProposal($context->land,
            [
                'state' => LandProposalWorkflowPlace::ARCHIVED,
                'title' => 'Archived Proposal 1',
                'gardenTotalSurface' => 100,
                'foodSecurityParticipation' => false,
                'sharingConditions' => [LandSharingCondition::GARDENING],
            ]);
        $landProposal2 = $this->createLandProposal($context->land,
            [
                'state' => LandProposalWorkflowPlace::ARCHIVED,
                'title' => 'Archived Proposal 2',
            ]);
        $landProposal3 = $this->createLandProposal($context->land,
            [
                'state' => LandProposalWorkflowPlace::DRAFT,
                'title' => 'Draft Proposal',
            ]); // Default state is DRAFT, but explicitly setting for clarity
        $landProposal4 = $this->createLandProposal($context->land,
            [
                'state' => LandProposalWorkflowPlace::PUBLISHED,
                'title' => 'Published Proposal',
            ]);

        $landProposal5 = $this->createLandProposal($context2->land,
            [
                'state' => LandProposalWorkflowPlace::PUBLISHED,
                'title' => 'Published Proposal',
            ]);

        // --- Test fetching the whole collection (as owner) ---
        $this->browser()->actingAs($context->owner) // Use the owner from the context
        ->get('/api/land_proposals', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 4) // Use totalItems for API Platform collections
            // Check properties of the first item (adjust index/order based on default sorting if needed)
            // Assuming default order might be by ID or createdAt, let's check against $landProposal1
            ->assertJsonMatches('member[0].ulid', $landProposal1->getUlid()->toString())
            ->assertJsonMatches('member[0].title', $landProposal1->getTitle())
            ->assertJsonMatches('member[0].description',
                $landProposal1->getDescription()) // Assuming description is set in createLandProposal or nullable
            ->assertJsonMatches('member[0].soilType', $landProposal1->getSoilType())
            ->assertJsonMatches('member[0].orientation', $landProposal1->getOrientation())
            ->assertJsonMatches('member[0].hasParking', $landProposal1->getHasParking())
            ->assertJsonMatches('member[0].hasTools', $landProposal1->getHasTools())
            ->assertJsonMatches('member[0].hasShed', $landProposal1->getHasShed())
            ->assertJsonMatches('member[0].hasWaterPoint', $landProposal1->getHasWaterPoint())
            ->assertJsonMatches('member[0].hasIndependentAccess', $landProposal1->getHasIndependentAccess())
            ->assertJsonMatches('member[0].gardenState', $landProposal1->getGardenState())
            ->assertJsonMatches('member[0].preferredInteractionMode',
                $landProposal1->getPreferredInteractionMode())
            ->assertJsonMatches('member[0].gardeningLevel', $landProposal1->getGardeningLevel())
            ->assertJsonMatches('member[0].lookingForGardenerLevel', $landProposal1->getLookingForGardenerLevel())
            ->assertJsonMatches('member[0].gardenTotalSurface', $landProposal1->getGardenTotalSurface())
            ->assertJsonMatches('member[0].foodSecurityParticipation',
                $landProposal1->getFoodSecurityParticipation())
            ->assertJsonMatches('member[0].sharingConditions', $landProposal1->getSharingConditions())
            ->assertJsonMatches('member[0].state', $landProposal1->getState()) // Should be ARCHIVED
            ->assertJsonMatches('member[0].expirationDate',
                $landProposal1->getExpirationDate()?->format(\DateTimeInterface::RFC3339_EXTENDED));

        // --- Test filtering by state: ARCHIVED ---
        $this->browser()->actingAs($context->owner) // Use the owner
        ->get('/api/land_proposals',
            ['query' => ['state' => LandProposalWorkflowPlace::ARCHIVED,
                'land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 2)
            // Check ULIDs to ensure the correct items are returned (order might vary)
            ->assertJsonMatches('member[0].ulid', $landProposal1->getUlid()->toString())
            ->assertJsonMatches('member[1].ulid', $landProposal2->getUlid()->toString());

        // --- Test filtering by state: DRAFT ---
        $this->browser()->actingAs($context->owner) // Use the owner
        ->get('/api/land_proposals',
            ['query' => ['state' => LandProposalWorkflowPlace::DRAFT,
                'land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 1)
            ->assertJsonMatches('member[0].ulid', $landProposal3->getUlid()->toString());

        // --- Test filtering by state: PUBLISHED ---
        $this->browser()->actingAs($context->owner) // Use the owner
        ->get('/api/land_proposals',
            ['query' => ['state' => LandProposalWorkflowPlace::PUBLISHED,
                'land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 1)
            ->assertJsonMatches('member[0].ulid', $landProposal4->getUlid()->toString());
    }

    public function testCollectionPublic()
    {
        $context1 = $this->createLandContext();
        $context2 = $this->createLandContext();
        $context3 = $this->createLandContext();

        $landProposal1 = $this->createLandProposal($context1->land);
        $landProposal2 = $this->createLandProposal($context1->land, ['state' => LandProposalWorkflowPlace::PUBLISHED]);

        $landProposal3 = $this->createLandProposal($context2->land);
        $landProposal4 = $this->createLandProposal($context2->land, ['state' => LandProposalWorkflowPlace::PUBLISHED]);

        $landProposal5 = $this->createLandProposal($context3->land);
        $landProposal6 = $this->createLandProposal($context3->land, ['state' => LandProposalWorkflowPlace::PUBLISHED]);

        $landProposal7 = $this->createLandProposal($context1->land, ['state' => LandProposalWorkflowPlace::ARCHIVED]);
        $landProposal8 = $this->createLandProposal($context1->land, ['state' => LandProposalWorkflowPlace::ARCHIVED]);
        $landProposal9 = $this->createLandProposal($context2->land, ['state' => LandProposalWorkflowPlace::ARCHIVED]);
        $landProposal10 = $this->createLandProposal($context3->land, ['state' => LandProposalWorkflowPlace::ARCHIVED]);

        $this->browser()->actingAs($context1->owner)
            ->get('/api/land_proposals/public')
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 2) // All published without the one from the connected user
            ->assertJsonMatches('member[0].state', LandProposalWorkflowPlace::PUBLISHED)
            ->assertJsonMatches('member[1].state', LandProposalWorkflowPlace::PUBLISHED);
    }

    public function testDelete(): void
    {
        $context = $this->createLandContext();
        $landProposal = $this->createLandProposal($context->land);

        $this->browser()->actingAs($context->owner)
            ->delete($this->getIriFromResource($landProposal))
            ->assertStatus(204);
    }

    public function testPublish(): void
    {
        $context = $this->createLandContext();
        $landProposal = $this->createLandProposal($context->land);

        $this->browser()->actingAs($context->owner)
            ->patch($this->getIriFromResource($landProposal).'/'.LandProposalWorkflowTransition::PUBLISH)
            ->assertSuccessful()
            ->assertJsonMatches('state', LandProposalWorkflowPlace::PUBLISHED)
            ->use(function (Json $json) {
                $json->assertThat('expirationDate', fn (Json $json) => $json->isNotNull());
                $json->assertThat('publishedAt', fn (Json $json) => $json->isNotNull());
            });
    }

    public function testArchive(): void
    {
        $context = $this->createLandContext();
        $landProposal = $this->createLandProposal($context->land, ['state' => LandProposalWorkflowPlace::PUBLISHED]);

        $this->browser()->actingAs($context->owner)
            ->patch($this->getIriFromResource($landProposal).'/'.LandProposalWorkflowTransition::ARCHIVE)
            ->assertSuccessful()
            ->assertJsonMatches('state', LandProposalWorkflowPlace::ARCHIVED)
            ->use(function (Json $json) {
                $json->assertThat('archivedAt', fn (Json $json) => $json->isNotNull());
            });
    }

    public function testCannotCreateSecondDraft(): void
    {
        $context = $this->createLandContext();
        $this->createLandProposal($context->land, ['state' => LandProposalWorkflowPlace::DRAFT]);

        $data = [
            'title' => 'My Awesome Garden Proposal',
            'description' => ['type' => 'doc',
                'content' => [['type' => 'paragraph',
                    'content' => [['type' => 'text',
                        'text' => 'Detailed description here.']]]]],
            // Example Tiptap JSON
            'soilType' => SoilType::LOAMY,
            'orientation' => Orientation::SOUTH,
            'hasParking' => true,
            'hasTools' => true,
            'hasShed' => false,
            'hasWaterPoint' => true,
            'hasIndependentAccess' => true,
            'gardenState' => 'Well maintained',
            'preferredInteractionMode' => LandInteractionMode::TOGETHER_BUT_NOT_ALL_TIME,
            'gardeningLevel' => GardeningLevel::BEGINNER,
            'lookingForGardenerLevel' => GardeningLevel::ADVANCED,
            'gardenTotalSurface' => 150,
            'foodSecurityParticipation' => true,
            'sharingConditions' => [LandSharingCondition::GARDENING, LandSharingCondition::VEGETABLE_SHARING],
            'land' => $this->getIriFromResource($context->land),
        ];

        $this->browser()->actingAs($context->owner)
            ->post('/api/land_proposals', ['json' => $data])
            ->assertStatus(422)
            ->assertJsonMatches('violations[0].message', 'You can only have one proposal in draft state.');
    }

    public function testCannotCreateSecondPublished(): void
    {
        $context = $this->createLandContext();
        $firstProposal = $this->createLandProposal($context->land, ['state' => LandProposalWorkflowPlace::PUBLISHED]);

        // Try to publish another proposal
        $secondProposal = $this->createLandProposal($context->land, ['state' => LandProposalWorkflowPlace::DRAFT]);

        $this->browser()->actingAs($context->owner)
            ->patch($this->getIriFromResource($secondProposal).'/'.LandProposalWorkflowTransition::PUBLISH)
            ->assertStatus(422)
            ->assertJsonMatches('violations[0].message', 'You can only have one proposal in published state.');
    }
}
