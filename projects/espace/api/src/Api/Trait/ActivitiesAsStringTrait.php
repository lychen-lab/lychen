<?php

namespace App\Api\Trait;

use App\Entity\AreaActivity;

trait ActivitiesAsStringTrait
{
    /**
     * @var string[]
     */
    public array $activities;

    /**
     * @param iterable<AreaActivity> $activities
     */
    public function setActivities(iterable $activities): void
    {
        $this->activities = [];
        foreach ($activities as $activity) {
            if ($code = $activity->getCode()) {
                $this->activities[] = $code;
            }
        }
    }
}
