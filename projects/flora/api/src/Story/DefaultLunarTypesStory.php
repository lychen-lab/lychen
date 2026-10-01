<?php

namespace App\Story;

use App\Factory\LunarTypeFactory;
use Zenstruck\Foundry\Story;

final class DefaultLunarTypesStory extends Story
{
    public const string ROOT = 'root';
    public const string LEAF = 'leaf';
    public const string FLOWER = 'flower';
    public const string SEED_AND_FRUIT = 'seed_and_fruit';

    public const array ALL = [
        self::ROOT,
        self::LEAF,
        self::FLOWER,
        self::SEED_AND_FRUIT,
    ];

    public function build(): void
    {
        foreach (self::ALL as $code) {
            $this->addState($code, LunarTypeFactory::new(['code' => $code])->create());
        }
    }
}
