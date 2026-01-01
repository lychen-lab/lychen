<?php

namespace App\Factory;

use App\Entity\AreaProposal;
use Override;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<AreaProposal>
 */
final class AreaProposalFactory extends PersistentObjectFactory
{
    #[Override]
    public static function class(): string
    {
        return AreaProposal::class;
    }

    #[Override]
    protected function defaults(): array|callable
    {
        return [
            'description' => self::faker()->text(),
            //'state' => AreaProposalWorkflowState::DRAFT,
            'title' => self::faker()->text(255),
        ];
    }

    #[Override]
    protected function initialize(): static
    {
        return $this;
    }
}
