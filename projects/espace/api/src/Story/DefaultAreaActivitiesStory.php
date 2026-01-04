<?php

namespace App\Story;

use App\Factory\AreaActivityFactory;
use Zenstruck\Foundry\Story;

final class DefaultAreaActivitiesStory extends Story
{
    public const string GENERAL_MAINTENANCE = 'general_maintenance';
    public const string GARDENING = 'gardening';
    public const string BEEHIVE = 'beehives';
    public const string VEGETABLE_SHARING = 'vegetable_sharing';
    public const string FRUIT_SHARING = 'fruit_sharing';
    public const string FLOWER_PLANTING = 'flower_planting';
    public const string TREE_PLANTING = 'tree_planting';

    public const array ALL = [
        self::GENERAL_MAINTENANCE,
        self::GARDENING,
        self::BEEHIVE,
        self::VEGETABLE_SHARING,
        self::FRUIT_SHARING,
        self::FLOWER_PLANTING,
        self::TREE_PLANTING,
    ];

    public function build(): void
    {
        foreach (self::ALL as $code) {
            $this->addState($code, AreaActivityFactory::new(['code' => $code])->create());
        }
    }
}
