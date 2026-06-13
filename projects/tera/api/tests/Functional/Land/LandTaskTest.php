<?php

namespace App\Tests\Functional\Land;

use App\Factory\LandTaskFactory;
use App\Repository\LandTaskRepository;
use App\Security\Voter\LandTaskVoter;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use App\Workflow\LandTask\LandTaskWorkflowPlace;
use App\Workflow\LandTask\LandTaskWorkflowTransition;
use Lychen\UtilTiptap\Service\TipTapFaker;
use Zenstruck\Browser\Json;
use function Zenstruck\Foundry\faker;

class LandTaskTest extends AbstractApiTestCase
{
    public function testPost()
    {
        $context = $this->createLandContext();

        $title = faker()->title();
        $content = TipTapFaker::paragraphs();

        // Owner
        $this->browser()->actingAs($context->owner)
            ->post('/api/land_tasks',
                ['json' => [
                    'title' => $title,
                    'content' => $content,
                    'land' => $this->getIriFromResource($context->land)
                ]])
            ->assertStatus(201)
            ->assertJsonMatches('title', $title)
            ->assertJsonMatches('content', $content)
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn(Json $json) => $json->isNotNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandTaskVoter::POST]);
        $this->addLandMember($context, [$landRole]);

        $title = faker()->title();
        $content = TipTapFaker::paragraphs();

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->post('/api/land_tasks',
                ['json' => [
                    'title' => $title,
                    'content' => $content,
                    'land' => $this->getIriFromResource($context->land)
                ]])
            ->assertStatus(201)
            ->assertJsonMatches('title', $title)
            ->assertJsonMatches('content', $content)
            ->use(function (Json $json) {
                $json->assertThat('ulid', fn(Json $json) => $json->isNotNull());
            });
    }

    public function testGet()
    {
        $context = $this->createLandContext();
        $this->addOneLandTask($context);

        $landTask = $context->landTasks[0];

        // Owner
        $this->browser()->actingAs($context->owner)
            ->get($this->getIriFromResource($landTask))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landTask->getUlid()->toString())
            ->assertJsonMatches('title', $landTask->getTitle())
            ->assertJsonMatches('content', $landTask->getContent())
            ->assertJsonMatches('state', $landTask->getState())
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandTaskVoter::GET]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get($this->getIriFromResource($landTask))
            ->assertSuccessful()
            ->assertJsonMatches('ulid', $landTask->getUlid()->toString())
            ->assertJsonMatches('title', $landTask->getTitle())
            ->assertJsonMatches('content', $landTask->getContent())
            ->assertJsonMatches('state', $landTask->getState())
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNull());
            });
    }

    public function testPatch()
    {
        $context = $this->createLandContext();
        $this->addOneLandTask($context);

        $landTask = $context->landTasks[0];

        $newTitle = faker()->title();
        $newContent = TipTapFaker::paragraphs();

        $this->browser()->actingAs($context->owner)
            ->patch($this->getIriFromResource($landTask),
                [
                    'json' => [
                        'title' => $newTitle,
                        'content' => $newContent
                    ]
                ])
            ->assertStatus(200)
            ->assertJsonMatches('ulid', $landTask->getUlid()->toString())
            ->assertJsonMatches('title', $newTitle)
            ->assertJsonMatches('content', $newContent)
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNotNull());
            });

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandTaskVoter::PATCH]);
        $this->addLandMember($context, [$landRole]);

        $newTitle = faker()->title();
        $newContent = TipTapFaker::paragraphs();

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->patch($this->getIriFromResource($landTask),
                [
                    'json' => [
                        'title' => $newTitle,
                        'content' => $newContent
                    ]
                ])
            ->assertStatus(200)
            ->assertJsonMatches('ulid', $landTask->getUlid()->toString())
            ->assertJsonMatches('title', $newTitle)
            ->assertJsonMatches('content', $newContent)
            ->use(function (Json $json) {
                $json->assertThat('createdAt', fn(Json $json) => $json->isNotNull());
                $json->assertThat('updatedAt', fn(Json $json) => $json->isNotNull());
            });
    }

    public function testCollection()
    {
        $context = $this->createLandContext();
        $this->addOneLandTask($context);
        $this->addOneLandTask($context);

        // Owner
        $this->browser()->actingAs($context->owner)
            ->get('/api/land_tasks', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', count($context->landTasks))
            ->assertJsonMatches('member[0].ulid', $context->landTasks[0]->getUlid()->toString())
            ->assertJsonMatches('member[0].title', $context->landTasks[0]->getTitle())
            //->assertJsonMatches('member[0].content', $context->landTasks[0]->getContent())
            ->assertJsonMatches('member[0].state', $context->landTasks[0]->getState())
            ->assertJsonMatches('member[1].ulid', $context->landTasks[1]->getUlid()->toString())
            ->assertJsonMatches('member[1].title', $context->landTasks[1]->getTitle())
            //->assertJsonMatches('member[1].content', $context->landTasks[1]->getContent())
            ->assertJsonMatches('member[1].state', $context->landTasks[1]->getState());

        // Member with permissions
        $landRole = $this->createLandRole($context->land, [LandTaskVoter::COLLECTION]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->get('/api/land_tasks', ['query' => ['land' => $context->land->getUlid()->toString()]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', count($context->landTasks))
            ->assertJsonMatches('member[0].ulid', $context->landTasks[0]->getUlid()->toString())
            ->assertJsonMatches('member[0].title', $context->landTasks[0]->getTitle())
            //->assertJsonMatches('member[0].content', $context->landTasks[0]->getContent())
            ->assertJsonMatches('member[0].state', $context->landTasks[0]->getState())
            ->assertJsonMatches('member[1].ulid', $context->landTasks[1]->getUlid()->toString())
            ->assertJsonMatches('member[1].title', $context->landTasks[1]->getTitle())
            //->assertJsonMatches('member[1].content', $context->landTasks[1]->getContent())
            ->assertJsonMatches('member[1].state', $context->landTasks[1]->getState());
    }

    public function testCollectionPagination()
    {
        $context = $this->createLandContext();
        array_map(fn() => $this->addOneLandTask($context), range(1, 25));

        $this->browser()->actingAs($context->owner)
            ->get('/api/land_tasks',
                ['query' => ['land' => $context->land->getUlid()->toString(), 'itemsPerPage' => 10, 'page' => 2]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 25)
            ->use(function (Json $json) {
                $json->assertThat('member', fn(Json $json) => $json->hasCount(10));
            });
    }

    public function testCollectionFilterPerState()
    {
        $context = $this->createLandContext();
        LandTaskFactory::new()->many(3)->create([
            'land' => $context->land,
            'state' => LandTaskWorkflowPlace::TO_BE_DONE
        ]);
        LandTaskFactory::new()->many(12)->create([
            'land' => $context->land,
            'state' => LandTaskWorkflowPlace::IN_PROGRESS
        ]);
        LandTaskFactory::new()->many(6)->create([
            'land' => $context->land,
            'state' => LandTaskWorkflowPlace::DONE
        ]);

        $this->browser()->actingAs($context->owner)
            ->get('/api/land_tasks',
                ['query' => ['land' => $context->land->getUlid()->toString(),
                             'state' => LandTaskWorkflowPlace::TO_BE_DONE]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 3);

        $this->browser()->actingAs($context->owner)
            ->get('/api/land_tasks',
                ['query' => ['land' => $context->land->getUlid()->toString(),
                             'state' => LandTaskWorkflowPlace::IN_PROGRESS]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 12);

        $this->browser()->actingAs($context->owner)
            ->get('/api/land_tasks',
                ['query' => ['land' => $context->land->getUlid()->toString(),
                             'state' => LandTaskWorkflowPlace::DONE]])
            ->assertSuccessful()
            ->assertJsonMatches('totalItems', 6);
    }

    public function testDelete()
    {
        $context = $this->createLandContext();
        $this->addOneLandTask($context);

        // Owner
        $this->browser()->actingAs($context->owner)
            ->delete($this->getIriFromResource($context->landTasks[0]))
            ->assertStatus(204);

        // Member with permissions
        $this->addOneLandTask($context);
        $landRole = $this->createLandRole($context->land, [LandTaskVoter::DELETE]);
        $this->addLandMember($context, [$landRole]);

        $this->browser()->actingAs($context->landMembers[0]->getPerson())
            ->delete($this->getIriFromResource($context->landTasks[1]))
            ->assertStatus(204);
    }

    public function testMarkAsDone()
    {
        $context = $this->createLandContext();
        $this->addOneLandTask($context, ['state' => LandTaskWorkflowPlace::TO_BE_DONE]);
        $landTaskRepository = static::getContainer()->get(LandTaskRepository::class);

        $landTask = $context->landTasks[0];
        $uri = $this->getIriFromResource($landTask) . '/' . LandTaskWorkflowTransition::MARK_AS_DONE;
        $this->browser()->actingAs($context->owner)
            ->patch(
                $uri,
                ['json' => []])
            ->assertSuccessful();
        $landTask = $landTaskRepository->findOneBy(['land' => $context->land,
                                                    'state' => LandTaskWorkflowPlace::DONE]);
        $this->assertNotNull($landTask);
    }

    public function testMarkAsInProgress()
    {
        $context = $this->createLandContext();
        $this->addOneLandTask($context, ['state' => LandTaskWorkflowPlace::TO_BE_DONE]);
        $landTaskRepository = static::getContainer()->get(LandTaskRepository::class);

        $landTask = $context->landTasks[0];
        $uri = $this->getIriFromResource($landTask) . '/' . LandTaskWorkflowTransition::MARK_AS_IN_PROGRESS;
        $this->browser()->actingAs($context->owner)
            ->patch(
                $uri,
                ['json' => []])
            ->assertSuccessful();
        $landTask = $landTaskRepository->findOneBy(['land' => $context->land,
                                                    'state' => LandTaskWorkflowPlace::IN_PROGRESS]);
        $this->assertNotNull($landTask);

        $uri = $this->getIriFromResource($landTask) . '/' . LandTaskWorkflowTransition::MARK_AS_DONE;
        $this->browser()->actingAs($context->owner)
            ->patch(
                $uri,
                ['json' => []])
            ->assertSuccessful();
        $landTask = $landTaskRepository->findOneBy(['land' => $context->land,
                                                    'state' => LandTaskWorkflowPlace::DONE]);
        $this->assertNotNull($landTask);
    }
}
