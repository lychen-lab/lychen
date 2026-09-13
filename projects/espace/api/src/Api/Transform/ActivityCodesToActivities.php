<?php

namespace App\Api\Transform;

use App\Entity\AreaActivity;
use App\Repository\AreaActivityRepository;
use Symfony\Component\ObjectMapper\TransformCallableInterface;

/**
 * Object mapper transform resolving the activity codes sent by clients into
 * AreaActivity entities; unknown codes are ignored. Mapping the result onto an
 * entity goes through its addActivity()/removeActivity() methods, which keeps
 * the collection in sync with the sent codes.
 *
 * @implements TransformCallableInterface<object, object>
 */
final readonly class ActivityCodesToActivities implements TransformCallableInterface
{
    public function __construct(private AreaActivityRepository $areaActivityRepository)
    {
    }

    /**
     * @return list<AreaActivity>
     */
    public function __invoke(mixed $value, object $source, ?object $target): mixed
    {
        if (!\is_array($value) || [] === $value) {
            return [];
        }

        return $this->areaActivityRepository->findBy(['code' => array_values(array_unique($value))]);
    }
}
