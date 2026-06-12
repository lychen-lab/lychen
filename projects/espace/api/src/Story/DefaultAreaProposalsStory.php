<?php

namespace App\Story;

use App\Factory\AreaProposalFactory;
use Zenstruck\Foundry\Story;

final class DefaultAreaProposalsStory extends Story
{
    public function build(): void
    {
        for ($i = 0; $i < 30; ++$i) {
            $this->addToPool('default', AreaProposalFactory::new([])->create());
        }
    }
}
