<?php

namespace App\Factory;

use App\Entity\AreaProposal;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

use function Zenstruck\Foundry\lazy;

/**
 * @extends PersistentObjectFactory<AreaProposal>
 */
final class AreaProposalFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return AreaProposal::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        $surfaceTotal = self::faker()->numberBetween(5, 1000);

        return [
            'description' => self::faker()->text(),
            'title' => self::faker()->text(120),
            'city' => self::faker()->city(),
            'surfaceToShare' => self::faker()->numberBetween(5, $surfaceTotal),
            'surfaceTotal' => $surfaceTotal,
            'altitude' => self::faker()->numberBetween(0, 1000),
            // Lazy so that tests overriding these relations don't need existing rows.
            'proposer' => lazy(static fn () => PersonFactory::randomOrCreate()),
            'activities' => lazy(static fn () => AreaActivityFactory::randomRangeOrCreate(1, 5)),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
