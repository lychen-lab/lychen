<?php

namespace App\Tests\Functional;

use App\Factory\CharacteristicFactory;
use App\Factory\SeedLotFactory;
use Lychen\Test\AbstractApiTestCase;

class CharacteristicTest extends AbstractApiTestCase
{
    public function testPostThenGetCollection(): void
    {
        $this->browser()
            ->post('/api/characteristics', ['json' => [
                'code' => 'early',
                'label' => 'Précoce',
            ]])
            ->assertStatus(201)
            ->assertJsonMatches('code', 'early')
            ->assertJsonMatches('label', 'Précoce');

        $this->browser()
            ->get('/api/characteristics')
            ->assertStatus(200)
            ->assertJsonMatches('totalItems', 1)
            ->assertJsonMatches('member[0].code', 'early');
    }

    public function testPostRejectsADuplicateCode(): void
    {
        CharacteristicFactory::createOne(['code' => 'early']);

        // Checked by #[UniqueEntity] on the entity, once the DTO is mapped onto it.
        $this->browser()
            ->post('/api/characteristics', ['json' => [
                'code' => 'early',
                'label' => 'Précoce',
            ]])
            ->assertStatus(422)
            ->assertJsonMatches("length(violations[?propertyPath=='code'])", 1);

        CharacteristicFactory::assert()->count(1);
    }

    public function testPatchUpdatesTheLabel(): void
    {
        $characteristic = CharacteristicFactory::createOne(['code' => 'early', 'label' => 'Hâtive']);

        $this->browser()
            ->patch('/api/characteristics/'.$characteristic->getUuid(), ['json' => ['label' => 'Précoce']])
            ->assertStatus(200)
            ->assertJsonMatches('code', 'early')
            ->assertJsonMatches('label', 'Précoce');
    }

    public function testDeleteUnlinksItFromTheSeedLots(): void
    {
        $characteristic = CharacteristicFactory::createOne();
        $lot = SeedLotFactory::createOne(['characteristics' => [$characteristic]]);

        $this->browser()
            ->delete('/api/characteristics/'.$characteristic->getUuid())
            ->assertStatus(204);

        $this->browser()
            ->get('/api/seed_lots/'.$lot->getUuid())
            ->assertStatus(200)
            ->assertJsonMatches('characteristics', []);
    }
}
