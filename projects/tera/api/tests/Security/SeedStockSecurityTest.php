<?php

namespace App\Tests\Security;

use App\Entity\LandApiKey;
use App\Entity\Person;
use App\Entity\PersonApiKey;
use App\Entity\SeedStock;
use App\Factory\SeedStockFactory;
use App\Security\Constant\LandMemberPermission;
use App\Security\Constant\PersonPermission;
use App\Tests\Utils\Abstract\AbstractApiTestCase;

class SeedStockSecurityTest extends AbstractApiTestCase
{
    public function testPut()
    {
        $this->markTestIncomplete('TODO');
    }

    public function testGet(): void
    {
        $person = $this->createPerson();
        $seedStock = $this->createSeedStock($person);
        $iri = $this->getIriFromResource($seedStock);

        $this->browser()
            ->get($iri)
            ->assertStatus(401);

        $this->browser()->actingAs($person)
            ->get($iri)
            ->assertStatus(200);

        $this->browser()->actingAs($this->createPerson())
            ->get($iri)
            ->assertStatus(403);

        // No permission gives an API key access to seed stocks, not even its own person's
        $this->browser()->actingAs($this->createPersonApiKey($person, ['permissions' => PersonPermission::ALL]))
            ->get($iri)
            ->assertStatus(403);

        $this->browser()->actingAs($this->createLandApiKey($this->createLandContext()->land, ['permissions' => LandMemberPermission::ALL]))
            ->get($iri)
            ->assertStatus(403);
    }

    public function testPatch(): void
    {
        $person = $this->createPerson();
        $seedStock = $this->createSeedStock($person);
        $iri = $this->getIriFromResource($seedStock);

        $this->browser()
            ->patch($iri, ['json' => ['name' => 'Tomatoes']])
            ->assertStatus(401);

        $this->browser()->actingAs($this->createPerson())
            ->patch($iri, ['json' => ['name' => 'Tomatoes']])
            ->assertStatus(403);

        $this->browser()->actingAs($this->createPersonApiKey($person, ['permissions' => PersonPermission::ALL]))
            ->patch($iri, ['json' => ['name' => 'Tomatoes']])
            ->assertStatus(403);

        // The owner passes the same expression as on GET and DELETE, but the response of a PATCH does
        // not serialize yet (SeedStock has no serialization groups: a circular reference through Person)
    }

    public function testDelete(): void
    {
        $person = $this->createPerson();
        $seedStock = $this->createSeedStock($person);
        $iri = $this->getIriFromResource($seedStock);

        $this->browser()
            ->delete($iri)
            ->assertStatus(401);

        $this->browser()->actingAs($this->createPerson())
            ->delete($iri)
            ->assertStatus(403);

        $this->browser()->actingAs($this->createPersonApiKey($person, ['permissions' => PersonPermission::ALL]))
            ->delete($iri)
            ->assertStatus(403);

        $this->browser()->actingAs($person)
            ->delete($iri)
            ->assertStatus(204);
    }

    public function testCollection(): void
    {
        $this->browser()
            ->get('/api/seed_stocks')
            ->assertStatus(401);

        $person = $this->createPerson();
        $seedStock = $this->createSeedStock($person);
        $otherPerson = $this->createPerson();
        $this->createSeedStock($otherPerson);

        $this->assertSame([$this->getIriFromResource($seedStock)], $this->listIris($person, '/api/seed_stocks'));
    }

    public function testCollectionWithPersonApiKeyListsNothing(): void
    {
        $person = $this->createPerson();
        $this->createSeedStock($person);
        $otherPerson = $this->createPerson();
        $this->createSeedStock($otherPerson);

        // The key's id is the other person's. No permission gives an API key access to seed stocks:
        // it sees neither its own person's nor those of the person whose id it has
        $this->giveNextRowTheIdOf(PersonApiKey::class, $otherPerson);
        $personApiKey = $this->createPersonApiKey($person, ['permissions' => PersonPermission::ALL]);
        $this->assertSame($otherPerson->getId(), $personApiKey->getId());

        $this->assertSame([], $this->listIris($personApiKey, '/api/seed_stocks'));
    }

    public function testCollectionWithLandApiKeyListsNothing(): void
    {
        $person = $this->createPerson();
        $this->createSeedStock($person);

        // A land API key stands for a land, not for the person whose id it has
        $this->giveNextRowTheIdOf(LandApiKey::class, $person);
        $landApiKey = $this->createLandApiKey($this->createLandContext()->land, ['permissions' => LandMemberPermission::ALL]);
        $this->assertSame($person->getId(), $landApiKey->getId());

        $this->assertSame([], $this->listIris($landApiKey, '/api/seed_stocks'));
    }

    private function createSeedStock(Person $person): SeedStock
    {
        return SeedStockFactory::new()->create(['person' => $person]);
    }
}
