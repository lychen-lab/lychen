<?php

namespace App\Story;

use App\Factory\AreaRequestFactory;
use Zenstruck\Foundry\Story;

final class DefaultAreaRequestsStory extends Story
{
    public function build(): void
    {
        for ($i = 0; $i < 30; ++$i) {
            $this->addToPool('default', AreaRequestFactory::new([])->create());
        }
    }
}
