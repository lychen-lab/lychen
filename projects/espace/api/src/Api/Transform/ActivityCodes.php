<?php

namespace App\Api\Transform;

use App\Entity\AreaActivity;

/**
 * Object mapper transform exposing activities by their code.
 */
final class ActivityCodes
{
    /**
     * @param iterable<AreaActivity> $activities
     *
     * @return list<string>
     */
    public static function fromActivities(iterable $activities): array
    {
        $codes = [];
        foreach ($activities as $activity) {
            if (null !== $code = $activity->getCode()) {
                $codes[] = $code;
            }
        }

        return $codes;
    }
}
