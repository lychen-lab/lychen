<?php

namespace App\Constant;

final class GardeningLevel
{
    public const string BEGINNER = 'beginner';
    public const string INTERMEDIATE = 'intermediate';
    public const string ADVANCED = 'advanced';

    public const array ALL = [
        self::BEGINNER,
        self::INTERMEDIATE,
        self::ADVANCED,
    ];
}
