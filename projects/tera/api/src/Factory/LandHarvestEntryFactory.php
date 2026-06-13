<?php

namespace App\Factory;

use App\Constant\HarvestQuality;
use App\Entity\LandHarvestEntry;
use Lychen\UtilTiptap\Service\TipTapFaker;
use Symfony\Component\Uid\Ulid;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<LandHarvestEntry>
 */
final class LandHarvestEntryFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return LandHarvestEntry::class;
    }

    protected function defaults(): array|callable
    {
        $harvestedAt = self::faker()->dateTimeBetween('-1 years', 'now');
        return [
            'notes' => TipTapFaker::randomContent(),
            'weight' => self::faker()->numberBetween(0, 10000),
            'harvestedAt' => $harvestedAt,
            'quality' => self::faker()->randomElement(HarvestQuality::ALL),
            'plantId' => new Ulid(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this// ->afterInstantiate(function(LandHarvestEntry $landHarvestEntry): void {})
            ;
    }
}
