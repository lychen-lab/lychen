<?php

namespace App\Tests\Functional;

use App\Factory\CharacteristicFactory;
use App\Factory\SeedLibraryFactory;
use App\Factory\SeedLotFactory;
use Lychen\Test\AbstractApiTestCase;
use Symfony\Component\Uid\Uuid;

class SeedLotTest extends AbstractApiTestCase
{
    public function testGetExposesTheRelationsByUuid(): void
    {
        $library = SeedLibraryFactory::createOne();
        $early = CharacteristicFactory::createOne();
        $heirloom = CharacteristicFactory::createOne();
        $lot = SeedLotFactory::createOne([
            'seedLibrary' => $library,
            'characteristics' => [$early, $heirloom],
        ]);

        $this->browser()
            ->get('/api/seed_lots/'.$lot->getUuid())
            ->assertStatus(200)
            ->assertJsonMatches('"@id"', '/api/seed_lots/'.$lot->getUuid())
            ->assertJsonMatches('uuid', (string) $lot->getUuid())
            ->assertJsonMatches('id', null)
            ->assertJsonMatches('seedLibrary', (string) $library->getUuid())
            ->assertJsonMatches('length(characteristics)', 2)
            ->assertJsonMatches(\sprintf("contains(characteristics, '%s')", $early->getUuid()), true)
            ->assertJsonMatches(\sprintf("contains(characteristics, '%s')", $heirloom->getUuid()), true);
    }

    public function testGetCollectionExposesTheRelationsByUuid(): void
    {
        $library = SeedLibraryFactory::createOne();
        SeedLotFactory::createMany(2, ['seedLibrary' => $library]);

        $this->browser()
            ->get('/api/seed_lots')
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 2)
            ->assertJsonMatches('member[0]."@type"', 'SeedLot')
            ->assertJsonMatches('member[0].seedLibrary', (string) $library->getUuid())
            ->assertJsonMatches('member[1].seedLibrary', (string) $library->getUuid());
    }

    public function testGetAnUnknownUuidIsNotFound(): void
    {
        $this->browser()
            ->get('/api/seed_lots/'.Uuid::v7())
            ->assertStatus(404);
    }

    public function testPostLinksTheRelationsByUuid(): void
    {
        $library = SeedLibraryFactory::createOne();
        $early = CharacteristicFactory::createOne();
        $heirloom = CharacteristicFactory::createOne();

        $this->browser()
            ->post('/api/seed_lots', ['json' => [
                'variety' => 'Tomate cœur de bœuf',
                'harvestYear' => 2026,
                'quantityInGrams' => 40,
                'seedLibrary' => (string) $library->getUuid(),
                'characteristics' => [(string) $early->getUuid(), (string) $heirloom->getUuid()],
            ]])
            ->assertStatus(201)
            ->assertJsonMatches('variety', 'Tomate cœur de bœuf')
            ->assertJsonMatches('harvestYear', 2026)
            ->assertJsonMatches('quantityInGrams', 40)
            ->assertJsonMatches('seedLibrary', (string) $library->getUuid())
            ->assertJsonMatches('length(characteristics)', 2)
            ->assertJsonMatches(\sprintf("contains(characteristics, '%s')", $early->getUuid()), true)
            ->assertJsonMatches(\sprintf("contains(characteristics, '%s')", $heirloom->getUuid()), true);

        SeedLotFactory::assert()->count(1, ['seedLibrary' => $library]);
    }

    public function testPostRequiresTheSeedLibrary(): void
    {
        $this->browser()
            ->post('/api/seed_lots', ['json' => [
                'variety' => 'Haricot coco',
            ]])
            ->assertStatus(422)
            ->assertJsonMatches("length(violations[?propertyPath=='seedLibrary'])", 1);

        SeedLotFactory::assert()->empty();
    }

    public function testPostRejectsUnknownUuids(): void
    {
        $known = CharacteristicFactory::createOne();

        $this->browser()
            ->post('/api/seed_lots', ['json' => [
                'variety' => 'Haricot coco',
                'seedLibrary' => (string) Uuid::v7(),
                'characteristics' => [(string) $known->getUuid(), (string) Uuid::v7()],
            ]])
            ->assertStatus(422)
            ->assertJsonMatches("length(violations[?propertyPath=='seedLibrary'])", 1)
            ->assertJsonMatches("length(violations[?propertyPath=='characteristics[0]'])", 0)
            ->assertJsonMatches("length(violations[?propertyPath=='characteristics[1]'])", 1);

        SeedLotFactory::assert()->empty();
    }

    public function testPostRejectsAMalformedUuid(): void
    {
        $this->browser()
            ->post('/api/seed_lots', ['json' => [
                'variety' => 'Haricot coco',
                'seedLibrary' => 'not-a-uuid',
            ]])
            ->assertStatus(422);

        SeedLotFactory::assert()->empty();
    }

    public function testPatchOnlyChangesTheSentRelations(): void
    {
        $from = SeedLibraryFactory::createOne();
        $to = SeedLibraryFactory::createOne();
        [$early, $heirloom, $hardy] = CharacteristicFactory::createMany(3);
        $lot = SeedLotFactory::createOne([
            'variety' => 'Haricot coco',
            'seedLibrary' => $from,
            'characteristics' => [$early, $heirloom],
        ]);
        $uri = '/api/seed_lots/'.$lot->getUuid();

        // No relation sent: both are left as they are.
        $this->browser()
            ->patch($uri, ['json' => ['quantityInGrams' => 10]])
            ->assertStatus(200)
            ->assertJsonMatches('quantityInGrams', 10)
            ->assertJsonMatches('variety', 'Haricot coco')
            ->assertJsonMatches('seedLibrary', (string) $from->getUuid())
            ->assertJsonMatches('length(characteristics)', 2);

        // The lot moves to another library, and the list replaces the collection.
        $this->browser()
            ->patch($uri, ['json' => [
                'seedLibrary' => (string) $to->getUuid(),
                'characteristics' => [(string) $hardy->getUuid()],
            ]])
            ->assertStatus(200)
            ->assertJsonMatches('seedLibrary', (string) $to->getUuid())
            ->assertJsonMatches('characteristics', [(string) $hardy->getUuid()]);

        // An empty list unlinks them all.
        $this->browser()
            ->patch($uri, ['json' => ['characteristics' => []]])
            ->assertStatus(200)
            ->assertJsonMatches('characteristics', [])
            ->assertJsonMatches('seedLibrary', (string) $to->getUuid());
    }

    public function testPatchNullCharacteristicsUnlinksThemAll(): void
    {
        $library = SeedLibraryFactory::createOne();
        $lot = SeedLotFactory::createOne([
            'variety' => 'Haricot coco',
            'seedLibrary' => $library,
            'characteristics' => CharacteristicFactory::createMany(2),
        ]);

        // JSON merge patch (RFC 7396): null removes the member, here every link.
        $this->browser()
            ->patch('/api/seed_lots/'.$lot->getUuid(), ['json' => ['characteristics' => null]])
            ->assertStatus(200)
            ->assertJsonMatches('characteristics', [])
            ->assertJsonMatches('seedLibrary', (string) $library->getUuid())
            ->assertJsonMatches('variety', 'Haricot coco');

        // Unlinked, not deleted.
        CharacteristicFactory::assert()->count(2);
    }

    public function testPatchRejectsANullOrUnknownSeedLibrary(): void
    {
        $library = SeedLibraryFactory::createOne();
        $lot = SeedLotFactory::createOne(['seedLibrary' => $library]);
        $uri = '/api/seed_lots/'.$lot->getUuid();

        $this->browser()
            ->patch($uri, ['json' => ['seedLibrary' => null]])
            ->assertStatus(422);

        $this->browser()
            ->patch($uri, ['json' => ['seedLibrary' => (string) Uuid::v7()]])
            ->assertStatus(422)
            ->assertJsonMatches("length(violations[?propertyPath=='seedLibrary'])", 1);

        SeedLotFactory::assert()->count(1, ['seedLibrary' => $library]);
    }

    public function testDeleteKeepsTheLinkedResources(): void
    {
        $lot = SeedLotFactory::createOne([
            'seedLibrary' => SeedLibraryFactory::createOne(),
            'characteristics' => [CharacteristicFactory::createOne()],
        ]);

        $this->browser()
            ->delete('/api/seed_lots/'.$lot->getUuid())
            ->assertStatus(204);

        SeedLotFactory::assert()->empty();
        SeedLibraryFactory::assert()->count(1);
        CharacteristicFactory::assert()->count(1);
    }
}
