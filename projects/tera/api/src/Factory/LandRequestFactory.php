<?php

namespace App\Factory;

use App\Constant\GardeningLevel;
use App\Constant\LandInteractionMode;
use App\Constant\LandSharingCondition;
use App\Entity\LandRequest;
use Lychen\UtilTiptap\Service\TipTapFaker;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<LandRequest>
 */
final class LandRequestFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return LandRequest::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        return [
            'message' => TipTapFaker::randomContent(),
            'minimumSurfaceWanted' => self::faker()->numberBetween(10, 500),
            'gardeningLevel' => self::faker()->randomElement(GardeningLevel::ALL),
            'hasTools' => self::faker()->boolean(),
            'title' => self::faker()->sentence(4),
            'preferredInteractionMode' => self::faker()->randomElement(LandInteractionMode::ALL),
            'supportsLocalFoodSecurity' => self::faker()->boolean(),
            'sharingConditions' => self::faker()->randomElements(LandSharingCondition::ALL,
                self::faker()->numberBetween(0, count(LandSharingCondition::ALL))),
        ];

    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(LandRequest $landRequest): void {})
            ;
    }
}
