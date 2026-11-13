<?php

namespace App\Story;

use App\Factory\CharacteristicFactory;
use App\Factory\CultivationFactory;
use App\Factory\ExposureFactory;
use App\Factory\MaturityFactory;
use App\Factory\PlantFactory;
use App\Factory\SeedLibraryFactory;
use App\Factory\SeedLotFactory;
use App\Factory\SpeciesFactory;
use App\Workflow\SeedLot\SeedLotWorkflow;
use Zenstruck\Foundry\Attribute\AsFixture;
use Zenstruck\Foundry\Story;

use function Zenstruck\Foundry\faker;
use function Zenstruck\Foundry\Persistence\flush_after;

/**
 * A database full enough for a load test (see load/README.md): the reference data and
 * the default plants, plus a volume of plants, seed libraries and seed lots.
 *
 * Loaded by name, never with the Doctrine fixtures:
 *
 *     bin/console foundry:load-fixtures load --append
 */
#[AsFixture(name: 'load')]
final class LoadTestStory extends Story
{
    private const int PLANTS = 2_000;
    private const int CHARACTERISTICS = 40;
    private const int SEED_LIBRARIES = 200;
    private const int SEED_LOTS = 20_000;

    /** Objects written per flush. */
    private const int BATCH = 500;

    public function build(): void
    {
        // Loading twice must not double the volume.
        if (SeedLibraryFactory::count() > 0) {
            return;
        }

        DefaultPlantsStory::load();

        $species = SpeciesFactory::all();
        $exposures = ExposureFactory::all();
        $maturities = MaturityFactory::all();
        $this->createInBatches(self::PLANTS, static fn () => PlantFactory::createOne([
            'name' => ucfirst((string) faker()->words(3, true)),
            'species' => faker()->randomElement($species),
            'medicinal' => faker()->boolean(20),
            'melliferous' => faker()->boolean(30),
            'cultivation' => CultivationFactory::new([
                'exposure' => faker()->randomElement($exposures),
                'maturity' => faker()->randomElement($maturities),
                'sowingMonths' => [3, 4, 5],
                'harvestingMonths' => [7, 8, 9],
                'minimalDaysToHarvest' => faker()->numberBetween(40, 90),
                'maximalDaysToHarvest' => faker()->numberBetween(90, 240),
            ]),
        ]));

        $characteristics = flush_after(
            static fn () => CharacteristicFactory::createMany(self::CHARACTERISTICS),
        );
        $libraries = flush_after(
            static fn () => SeedLibraryFactory::createMany(self::SEED_LIBRARIES),
        );
        $states = [
            SeedLotWorkflow::PLACE_AVAILABLE,
            SeedLotWorkflow::PLACE_AVAILABLE,
            SeedLotWorkflow::PLACE_RESERVED,
            SeedLotWorkflow::PLACE_GIVEN,
        ];
        $this->createInBatches(self::SEED_LOTS, static fn () => SeedLotFactory::createOne([
            'seedLibrary' => faker()->randomElement($libraries),
            'characteristics' => faker()->randomElements($characteristics, faker()->numberBetween(0, 3)),
            'state' => faker()->randomElement($states),
        ]));
    }

    /**
     * @param callable(): mixed $create
     */
    private function createInBatches(int $count, callable $create): void
    {
        for ($done = 0; $done < $count; $done += self::BATCH) {
            flush_after(static function () use ($create, $count, $done): void {
                for ($i = 0; $i < min(self::BATCH, $count - $done); ++$i) {
                    $create();
                }
            });
        }
    }
}
