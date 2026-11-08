<?php

namespace App\Tests\Functional;

use App\Factory\SeedLibraryFactory;
use App\Factory\SeedLotFactory;
use App\Workflow\SeedLot\SeedLotWorkflow;
use Lychen\Api\State\WorkflowTransitionProcessor;
use Lychen\Test\AbstractApiTestCase;
use Symfony\Component\Uid\Uuid;

/**
 * SeedLot's workflow transitions, one PATCH each, handled by WorkflowTransitionProcessor
 * on the entity behind the mapped resource.
 *
 * @see WorkflowTransitionProcessor
 */
class SeedLotWorkflowTest extends AbstractApiTestCase
{
    public function testANewLotIsAvailable(): void
    {
        $library = SeedLibraryFactory::createOne();

        $this->browser()
            ->post('/api/seed_lots', ['json' => [
                'variety' => 'Fève',
                'seedLibrary' => (string) $library->getUuid(),
            ]])
            ->assertStatus(201)
            ->assertJsonMatches('state', SeedLotWorkflow::PLACE_AVAILABLE);
    }

    public function testReserveThenGiveAnswersWithTheMappedResource(): void
    {
        $library = SeedLibraryFactory::createOne();
        $lot = SeedLotFactory::createOne(['variety' => 'Fève', 'seedLibrary' => $library]);
        $uri = '/api/seed_lots/'.$lot->getUuid();

        $this->browser()
            ->patch($uri.'/'.SeedLotWorkflow::TRANSITION_RESERVE)
            ->assertStatus(200)
            ->assertJsonMatches('"@id"', $uri)
            ->assertJsonMatches('state', SeedLotWorkflow::PLACE_RESERVED)
            ->assertJsonMatches('variety', 'Fève')
            ->assertJsonMatches('seedLibrary', (string) $library->getUuid());

        $this->browser()
            ->patch($uri.'/'.SeedLotWorkflow::TRANSITION_GIVE)
            ->assertStatus(200)
            ->assertJsonMatches('state', SeedLotWorkflow::PLACE_GIVEN);

        $this->browser()
            ->get($uri)
            ->assertStatus(200)
            ->assertJsonMatches('state', SeedLotWorkflow::PLACE_GIVEN);
    }

    public function testReleaseMakesTheLotAvailableAgain(): void
    {
        $lot = SeedLotFactory::createOne();
        $uri = '/api/seed_lots/'.$lot->getUuid();

        $this->browser()
            ->patch($uri.'/'.SeedLotWorkflow::TRANSITION_RESERVE)
            ->assertStatus(200);

        $this->browser()
            ->patch($uri.'/'.SeedLotWorkflow::TRANSITION_RELEASE)
            ->assertStatus(200)
            ->assertJsonMatches('state', SeedLotWorkflow::PLACE_AVAILABLE);
    }

    public function testATransitionTheStateDoesNotAllowIsAConflict(): void
    {
        $lot = SeedLotFactory::createOne();
        $uri = '/api/seed_lots/'.$lot->getUuid();

        // An available lot must be reserved before it is given.
        $this->browser()
            ->patch($uri.'/'.SeedLotWorkflow::TRANSITION_GIVE)
            ->assertStatus(409);

        $this->browser()
            ->get($uri)
            ->assertJsonMatches('state', SeedLotWorkflow::PLACE_AVAILABLE);
    }

    public function testATransitionOnAnUnknownLotIsNotFound(): void
    {
        $this->browser()
            ->patch('/api/seed_lots/'.Uuid::v7().'/'.SeedLotWorkflow::TRANSITION_RESERVE)
            ->assertStatus(404);
    }
}
