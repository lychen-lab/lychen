<?php

namespace App\Story;

use App\Factory\AreaProposalFactory;
use App\Workflow\AreaProposal\AreaProposalWorkflow;
use Zenstruck\Foundry\Story;

final class DefaultAreaProposalsStory extends Story
{
    public function build(): void
    {
        for ($i = 0; $i < 30; ++$i) {
            // Other users only see published proposals: keep most of them published so
            // listings aren't empty in dev, plus a few drafts only their proposer sees.
            $place = 0 === $i % 5 ? AreaProposalWorkflow::PLACE_DRAFT : AreaProposalWorkflow::PLACE_PUBLISHED;
            $this->addToPool('default', AreaProposalFactory::new(['place' => $place])->create());
        }
    }
}
