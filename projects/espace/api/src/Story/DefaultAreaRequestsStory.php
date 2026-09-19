<?php

namespace App\Story;

use App\Factory\AreaRequestFactory;
use App\Workflow\AreaRequest\AreaRequestWorkflow;
use Zenstruck\Foundry\Story;

final class DefaultAreaRequestsStory extends Story
{
    public function build(): void
    {
        for ($i = 0; $i < 30; ++$i) {
            // Other users only see active requests: keep most of them active so
            // listings aren't empty in dev, plus a few drafts only their requester sees.
            $place = 0 === $i % 5 ? AreaRequestWorkflow::PLACE_DRAFT : AreaRequestWorkflow::PLACE_ACTIVE;
            $this->addToPool('default', AreaRequestFactory::new(['place' => $place])->create());
        }
    }
}
