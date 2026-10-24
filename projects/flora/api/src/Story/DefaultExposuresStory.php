<?php

namespace App\Story;

use App\Factory\ExposureFactory;
use Zenstruck\Foundry\Story;

final class DefaultExposuresStory extends Story
{
    public const string FULL_SUN = 'full_sun';
    public const string PARTIAL_SHADE = 'partial_shade';
    public const string SHADE = 'shade';
    public const string BRIGHT_INDIRECT = 'bright_indirect';
    public const string ADAPTABLE = 'adaptable';

    public const array ALL = [
        self::FULL_SUN,
        self::PARTIAL_SHADE,
        self::SHADE,
        self::BRIGHT_INDIRECT,
        self::ADAPTABLE,
    ];

    public function build(): void
    {
        foreach (self::ALL as $code) {
            $this->addState($code, ExposureFactory::new(['code' => $code])->create());
        }
    }
}
