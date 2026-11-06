<?php

namespace App\Tests\Functional;

use App\Factory\SeedLibraryFactory;
use App\Factory\SeedLotFactory;
use Lychen\Test\AbstractApiTestCase;

class SeedLibraryTest extends AbstractApiTestCase
{
    public function testGetExposesTheUuidsOfItsSeedLots(): void
    {
        $library = SeedLibraryFactory::createOne(['name' => 'Grainothèque du quartier']);
        $tomato = SeedLotFactory::createOne(['seedLibrary' => $library]);
        $bean = SeedLotFactory::createOne(['seedLibrary' => $library]);
        SeedLotFactory::createOne();

        $this->browser()
            ->get('/api/seed_libraries/'.$library->getUuid())
            ->assertStatus(200)
            ->assertJsonMatches('"@id"', '/api/seed_libraries/'.$library->getUuid())
            ->assertJsonMatches('uuid', (string) $library->getUuid())
            ->assertJsonMatches('id', null)
            ->assertJsonMatches('name', 'Grainothèque du quartier')
            ->assertJsonMatches('length(seedLots)', 2)
            ->assertJsonMatches(\sprintf("contains(seedLots, '%s')", $tomato->getUuid()), true)
            ->assertJsonMatches(\sprintf("contains(seedLots, '%s')", $bean->getUuid()), true);
    }

    public function testPostCreatesALibraryWithoutLots(): void
    {
        $this->browser()
            ->post('/api/seed_libraries', ['json' => [
                'name' => 'Grainothèque de la Croix-Rousse',
                'city' => 'Lyon',
            ]])
            ->assertStatus(201)
            ->assertJsonMatches('name', 'Grainothèque de la Croix-Rousse')
            ->assertJsonMatches('city', 'Lyon')
            ->assertJsonMatches('seedLots', [])
            ->assertJsonMatches('type(uuid)', 'string');

        SeedLibraryFactory::assert()->count(1, ['name' => 'Grainothèque de la Croix-Rousse']);
    }

    public function testPostRejectsABlankName(): void
    {
        $this->browser()
            ->post('/api/seed_libraries', ['json' => ['name' => '']])
            ->assertStatus(422)
            ->assertJsonMatches("length(violations[?propertyPath=='name'])", 1);

        SeedLibraryFactory::assert()->empty();
    }

    public function testPatchOnlyUpdatesTheSentFields(): void
    {
        $library = SeedLibraryFactory::createOne(['name' => 'Avant', 'city' => 'Lyon']);
        $uri = '/api/seed_libraries/'.$library->getUuid();

        $this->browser()
            ->patch($uri, ['json' => ['name' => 'Après']])
            ->assertStatus(200)
            ->assertJsonMatches('name', 'Après')
            ->assertJsonMatches('city', 'Lyon');

        // A nullable field accepts null, which clears it.
        $this->browser()
            ->patch($uri, ['json' => ['city' => null]])
            ->assertStatus(200)
            ->assertJsonMatches('name', 'Après')
            ->assertJsonMatches('city', null);

        // A non-nullable one rejects it.
        $this->browser()
            ->patch($uri, ['json' => ['name' => null]])
            ->assertStatus(422);
    }

    public function testDeleteRemovesItsSeedLots(): void
    {
        $library = SeedLibraryFactory::createOne();
        SeedLotFactory::createMany(2, ['seedLibrary' => $library]);
        SeedLotFactory::createOne();

        $this->browser()
            ->delete('/api/seed_libraries/'.$library->getUuid())
            ->assertStatus(204);

        SeedLibraryFactory::assert()->count(1);
        SeedLotFactory::assert()->count(1);
    }
}
