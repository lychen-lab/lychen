<?php

namespace App\Api\Trait;

use App\Api\Transform\ActivityCodes;
use Symfony\Component\ObjectMapper\Attribute\Map;

trait ActivitiesAsStringTrait
{
    /**
     * @var list<string>
     */
    #[Map(source: 'activities', transform: [ActivityCodes::class, 'fromActivities'])]
    public array $activities = [];
}
