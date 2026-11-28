<?php

namespace App\Tests\Functional\Plant;

use App\ApiResource\Plant;
use App\Service\Flora\FloraPlantClient;
use App\Service\Flora\FloraUnavailableException;
use App\Tests\Utils\Abstract\AbstractApiTestCase;
use Symfony\Component\Uid\Ulid;

/**
 * Tests tera's GET /api/plants/{ulid} (routing, security, PlantProvider, serialization)
 * with FloraPlantClient mocked: Flora is never called (FLORA_API_URL points nowhere in
 * the test environment).
 */
class PlantTest extends AbstractApiTestCase
{
    public function testGet(): void
    {
        $ulid = new Ulid()->toString();

        // A mock: checks that the provider asks Flora for this very plant, once.
        $floraPlantClient = $this->createMock(FloraPlantClient::class);
        $floraPlantClient->expects($this->once())
            ->method('find')
            ->with($ulid)
            ->willReturn(new Plant(ulid: $ulid, name: 'Tomate', melliferous: true, medicinal: false));

        $this->browser()
            ->addMock(FloraPlantClient::class, $floraPlantClient)
            ->actingAs($this->createPerson())
            ->get('/api/plants/'.$ulid)
            ->assertStatus(200)
            ->assertJsonMatches('"@id"', '/api/plants/'.$ulid)
            ->assertJsonMatches('"@type"', 'Plant')
            ->assertJsonMatches('ulid', $ulid)
            ->assertJsonMatches('name', 'Tomate')
            ->assertJsonMatches('melliferous', true)
            ->assertJsonMatches('medicinal', false);
    }

    public function testGetUnknownPlant(): void
    {
        // A stub: only gives Flora's answer, no expectation on the call.
        $floraPlantClient = $this->createStub(FloraPlantClient::class);
        $floraPlantClient->method('find')->willReturn(null);

        $this->browser()
            ->addMock(FloraPlantClient::class, $floraPlantClient)
            ->actingAs($this->createPerson())
            ->get('/api/plants/'.new Ulid()->toString())
            ->assertStatus(404);
    }

    public function testGetWhileFloraIsDown(): void
    {
        $floraPlantClient = $this->createStub(FloraPlantClient::class);
        $floraPlantClient->method('find')->willThrowException(new FloraUnavailableException('Flora is down.'));

        $this->browser()
            ->addMock(FloraPlantClient::class, $floraPlantClient)
            ->actingAs($this->createPerson())
            ->get('/api/plants/'.new Ulid()->toString())
            ->assertStatus(503);
    }

    public function testGetAnonymously(): void
    {
        // The firewall refuses the request before the provider: Flora is not even asked.
        $floraPlantClient = $this->createMock(FloraPlantClient::class);
        $floraPlantClient->expects($this->never())->method('find');

        $this->browser()
            ->addMock(FloraPlantClient::class, $floraPlantClient)
            ->get('/api/plants/'.new Ulid()->toString())
            ->assertStatus(401);
    }
}
