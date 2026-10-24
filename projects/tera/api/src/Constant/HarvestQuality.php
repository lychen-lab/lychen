<?php

namespace App\Constant;

final class HarvestQuality
{
    public const string VERY_POOR = 'very_poor';
    public const string POOR = 'poor';
    public const string STANDARD = 'standard';
    public const string GOOD = 'good';
    public const string EXCELLENT = 'excellent';

    public const array ALL = [
        self::VERY_POOR,
        self::POOR,
        self::STANDARD,
        self::GOOD,
        self::EXCELLENT,
    ];
}
