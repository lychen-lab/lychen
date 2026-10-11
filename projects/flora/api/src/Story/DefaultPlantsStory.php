<?php

namespace App\Story;

use App\Factory\CultivationFactory;
use App\Factory\PlantFactory;
use App\Factory\PlantPartFactory;
use Zenstruck\Foundry\Story;

final class DefaultPlantsStory extends Story
{
    public const string PLANT_1 = 'plant_1';
    public const string PLANT_2 = 'plant_2';
    public const string PLANT_3 = 'plant_3';
    public const string PLANT_4 = 'plant_4';
    public const string PLANT_5 = 'plant_5';
    public const string PLANT_6 = 'plant_6';
    public const string PLANT_7 = 'plant_7';

    public function build(): void
    {
        $this->createPlant(
            self::PLANT_1,
            [
                'name' => 'Concombre du Kenya Kiwano',
                'perennial' => false,
                'species' => DefaultSpeciesStory::get(DefaultSpeciesStory::CUCUMIS_METULIFERUS),
                'medicinal' => false,
                'melliferous' => false,
                'soilTypes' => [
                    DefaultSoilTypesStory::get(DefaultSoilTypesStory::LOAMY),
                ],
            ],
            [
                'sowingMonths' => [3, 4, 5],
                'sowingMinimalTemperature' => 22,
                'sowingMaximalTemperature' => 25,
                'minimalDaysToGermination' => 5,
                'maximalDaysToGermination' => 10,
                'harvestingMonths' => [8, 9],
                'minimalDaysToHarvest' => 120,
                'maximalDaysToHarvest' => 150,
                'maturity' => DefaultMaturitiesStory::get(DefaultMaturitiesStory::STANDARD),
                'exposure' => DefaultExposuresStory::get(DefaultExposuresStory::FULL_SUN),
                'vegetationTemperatureThreshold' => null,
            ],
            [
                [
                    'part' => DefaultPartsStory::get(DefaultPartsStory::FRUIT),
                    'consumable' => true,
                    'consumptionMethods' => [
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::RAW),
                    ],
                ],
            ]
        );

        $this->createPlant(
            self::PLANT_2,
            [
                'name' => 'Laitue Feuille de Chêne Blonde',
                'perennial' => false,
                'species' => DefaultSpeciesStory::get(DefaultSpeciesStory::LACTUCA_SATIVA),
                'medicinal' => false,
                'melliferous' => false,
                'soilTypes' => [
                    DefaultSoilTypesStory::get(DefaultSoilTypesStory::HUMIFEROUS),
                ],
            ],
            [
                'sowingMonths' => [2, 3, 4, 5, 6, 7, 8, 9],
                'sowingMinimalTemperature' => 10,
                'sowingMaximalTemperature' => 18,
                'minimalDaysToGermination' => 7,
                'maximalDaysToGermination' => 10,
                'harvestingMonths' => [4, 5, 6, 7, 8, 9, 10],
                'minimalDaysToHarvest' => 40,
                'maximalDaysToHarvest' => 50,
                'maturity' => DefaultMaturitiesStory::get(DefaultMaturitiesStory::STANDARD),
                'exposure' => DefaultExposuresStory::get(DefaultExposuresStory::PARTIAL_SHADE),
                'vegetationTemperatureThreshold' => null,
            ],
            [
                [
                    'part' => DefaultPartsStory::get(DefaultPartsStory::LEAF),
                    'consumable' => true,
                    'consumptionMethods' => [
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::RAW),
                    ],
                ],
            ]
        );

        $this->createPlant(self::PLANT_3,
            [
                'name' => 'Artichaut Imperial Star',
                'perennial' => true,
                'species' => DefaultSpeciesStory::get(DefaultSpeciesStory::CYNARA_CARDUNCIUS),
                'medicinal' => false,
                'melliferous' => false,
                'soilTypes' => [
                    DefaultSoilTypesStory::get(DefaultSoilTypesStory::LOAMY),
                ],
            ],
            [
                'sowingMonths' => [2, 3, 4],
                'sowingMinimalTemperature' => 20,
                'sowingMaximalTemperature' => 25,
                'minimalDaysToGermination' => 15,
                'maximalDaysToGermination' => 20,
                'harvestingMonths' => [5, 6, 7, 8, 9, 10],
                'minimalDaysToHarvest' => 56,
                'maximalDaysToHarvest' => 70,
                'maturity' => DefaultMaturitiesStory::get(DefaultMaturitiesStory::STANDARD),
                'exposure' => DefaultExposuresStory::get(DefaultExposuresStory::FULL_SUN),
                'vegetationTemperatureThreshold' => null,
            ],
            [
                [
                    'part' => DefaultPartsStory::get(DefaultPartsStory::FRUIT),
                    'consumable' => true,
                    'consumptionMethods' => [
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::COOKED),
                    ],
                ],
            ]
        );

        $this->createPlant(self::PLANT_4,
            [
                'name' => 'Carotte violette Gniff',
                'perennial' => false,
                'species' => DefaultSpeciesStory::get(DefaultSpeciesStory::DAUCUS_CAROTA),
                'medicinal' => false,
                'melliferous' => false,
                'soilTypes' => [
                    DefaultSoilTypesStory::get(DefaultSoilTypesStory::SANDY),
                ],
            ],
            [
                'sowingMonths' => [2, 3, 4, 5],
                'sowingMinimalTemperature' => 10,
                'sowingMaximalTemperature' => 30,
                'minimalDaysToGermination' => 8,
                'maximalDaysToGermination' => 15,
                'harvestingMonths' => [6, 7, 8, 9, 10, 11],
                'minimalDaysToHarvest' => 75,
                'maximalDaysToHarvest' => 240,
                'maturity' => DefaultMaturitiesStory::get(DefaultMaturitiesStory::STANDARD),
                'exposure' => DefaultExposuresStory::get(DefaultExposuresStory::FULL_SUN),
                'vegetationTemperatureThreshold' => null,
            ],
            [
                [
                    'part' => DefaultPartsStory::get(DefaultPartsStory::ROOT),
                    'consumable' => true,
                    'consumptionMethods' => [
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::COOKED),
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::RAW),
                    ],
                ],
                [
                    'part' => DefaultPartsStory::get(DefaultPartsStory::LEAF),
                    'consumable' => true,
                    'consumptionMethods' => [
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::COOKED),
                    ],
                ],
            ]
        );

        $this->createPlant(self::PLANT_5,
            [
                'name' => 'Haricot à rames mangetout à cosses violettes',
                'perennial' => false,
                'species' => DefaultSpeciesStory::get(DefaultSpeciesStory::PHASEOLUS_VULGARIS),
                'medicinal' => false,
                'melliferous' => false,
                'soilTypes' => [
                    DefaultSoilTypesStory::get(DefaultSoilTypesStory::HUMIFEROUS),
                ],
            ],
            [
                'sowingMonths' => [5, 6],
                'sowingMinimalTemperature' => 16,
                'sowingMaximalTemperature' => 25,
                'minimalDaysToGermination' => 8,
                'maximalDaysToGermination' => 12,
                'harvestingMonths' => [8, 9],
                'minimalDaysToHarvest' => 90,
                'maximalDaysToHarvest' => 120,
                'maturity' => DefaultMaturitiesStory::get(DefaultMaturitiesStory::STANDARD),
                'exposure' => DefaultExposuresStory::get(DefaultExposuresStory::FULL_SUN),
                'vegetationTemperatureThreshold' => null,
            ],
            [
                [
                    'part' => DefaultPartsStory::get(DefaultPartsStory::FRUIT),
                    'consumable' => true,
                    'consumptionMethods' => [
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::COOKED),
                    ],
                ],
            ]
        );

        $this->createPlant(self::PLANT_6,
            [
                'name' => 'Basilic Grand vert',
                'perennial' => false,
                'species' => DefaultSpeciesStory::get(DefaultSpeciesStory::OCIMUM_BASILICUM),
                'medicinal' => true,
                'melliferous' => true,
                'soilTypes' => [
                    DefaultSoilTypesStory::get(DefaultSoilTypesStory::LOAMY),
                ],
            ],
            [
                'sowingMonths' => [3, 4, 5],
                'sowingMinimalTemperature' => 20,
                'sowingMaximalTemperature' => 20,
                'minimalDaysToGermination' => 10,
                'maximalDaysToGermination' => 10,
                'harvestingMonths' => [6, 7, 8, 9, 10],
                'minimalDaysToHarvest' => 60,
                'maximalDaysToHarvest' => 60,
                'maturity' => DefaultMaturitiesStory::get(DefaultMaturitiesStory::STANDARD),
                'exposure' => DefaultExposuresStory::get(DefaultExposuresStory::PARTIAL_SHADE),
                'vegetationTemperatureThreshold' => null,
            ],
            [
                [
                    'part' => DefaultPartsStory::get(DefaultPartsStory::FLOWER),
                    'consumable' => true,
                    'consumptionMethods' => [
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::COOKED),
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::DRIED),
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::RAW),
                    ],
                ],
                [
                    'part' => DefaultPartsStory::get(DefaultPartsStory::LEAF),
                    'consumable' => true,
                    'consumptionMethods' => [
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::COOKED),
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::DRIED),
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::RAW),
                    ],
                ],
                [
                    'part' => DefaultPartsStory::get(DefaultPartsStory::STEM),
                    'consumable' => true,
                    'consumptionMethods' => [
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::COOKED),
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::DRIED),
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::RAW),
                    ],
                ],
            ]
        );

        $this->createPlant(self::PLANT_7,
            [
                'name' => 'Bleuet bleu',
                'perennial' => false,
                'species' => DefaultSpeciesStory::get(DefaultSpeciesStory::CENTAUREA_CYANUS),
                'medicinal' => true,
                'melliferous' => true,
                'soilTypes' => DefaultSoilTypesStory::getPool('all'),
            ],
            [
                'sowingMonths' => [3, 4, 5, 8, 9, 10],
                'sowingMinimalTemperature' => 18,
                'sowingMaximalTemperature' => 25,
                'minimalDaysToGermination' => 20,
                'maximalDaysToGermination' => 20,
                'harvestingMonths' => [6, 7, 8],
                'minimalDaysToHarvest' => 50,
                'maximalDaysToHarvest' => 80,
                'maturity' => DefaultMaturitiesStory::get(DefaultMaturitiesStory::STANDARD),
                'exposure' => DefaultExposuresStory::get(DefaultExposuresStory::FULL_SUN),
                'vegetationTemperatureThreshold' => null,
            ],
            [
                [
                    'part' => DefaultPartsStory::get(DefaultPartsStory::FLOWER),
                    'consumable' => true,
                    'consumptionMethods' => [
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::COOKED),
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::DRIED),
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::RAW),
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::INFUSED),
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::FERMENTED),
                    ],
                ],
                [
                    'part' => DefaultPartsStory::get(DefaultPartsStory::PETAL),
                    'consumable' => true,
                    'consumptionMethods' => [
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::COOKED),
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::DRIED),
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::RAW),
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::INFUSED),
                        DefaultConsumptionMethodsStory::get(DefaultConsumptionMethodsStory::FERMENTED),
                    ],
                ],
            ]
        );
    }

    protected function createPlant(string $stateId,
        array $attributes,
        ?array $cultivationAttributes = [],
        ?array $plantPartList = []): void
    {
        $plant = PlantFactory::new([
            ...$attributes,
            'cultivation' => CultivationFactory::new($cultivationAttributes),
        ])->create();
        $this->addState(
            $stateId,
            $plant
        );
        foreach ($plantPartList as $plantPart) {
            PlantPartFactory::new([...$plantPart, 'plant' => $plant])->create();
        }
    }
}
