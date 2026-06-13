<?php

namespace App\Factory;

use App\Constant\GardeningLevel;
use App\Constant\LandInteractionMode;
use App\Constant\LandSharingCondition;
use App\Constant\Orientation;
use App\Constant\SoilType;
use App\Entity\LandProposal;
use Lychen\UtilTiptap\Service\TipTapFaker;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<LandProposal>
 */
final class LandProposalFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return LandProposal::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'description' => TipTapFaker::randomContent(),
            'orientation' => self::faker()->randomElement(Orientation::ALL),
            'soilType' => self::faker()->randomElement(SoilType::ALL),
            'gardenTotalSurface' => self::faker()->numberBetween(10, 500),
            'gardeningLevel' => self::faker()->randomElement(GardeningLevel::ALL),
            'lookingForGardenerLevel' => self::faker()->randomElement(GardeningLevel::ALL),
            'hasTools' => self::faker()->boolean(),
            'hasParking' => self::faker()->boolean(),
            'hasShed' => self::faker()->boolean(),
            'hasWaterPoint' => self::faker()->boolean(),
            'hasIndependentAccess' => self::faker()->boolean(),
            'title' => self::faker()->sentence(4),
            'gardenState' => self::faker()->sentence(3),
            'preferredInteractionMode' => self::faker()->randomElement(LandInteractionMode::ALL),
            'foodSecurityParticipation' => self::faker()->boolean(),
            'sharingConditions' => self::faker()->randomElements(LandSharingCondition::ALL,
                self::faker()->numberBetween(2, count(LandSharingCondition::ALL))),
        ];

    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(LandProposal $landProposal): void {})
            ;
    }
}
