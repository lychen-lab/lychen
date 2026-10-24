<?php

namespace App\Constant;

final class SoilType
{
    public const string SANDY = 'sandy';
    public const string HUMUS_RICH = 'humus_rich';
    public const string CLAY = 'clay';
    public const string SILTY = 'silty';
    public const string LOAMY = 'loamy';
    public const string STONY = 'stony';
    public const string PEATY = 'peaty';
    public const string CHALKY = 'chalky';

    public const array ALL = [
        self::SANDY,
        self::HUMUS_RICH,
        self::CLAY,
        self::SILTY,
        self::LOAMY,
        self::STONY,
        self::PEATY,
        self::CHALKY,
    ];
}
