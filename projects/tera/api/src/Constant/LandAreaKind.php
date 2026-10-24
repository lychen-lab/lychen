<?php

namespace App\Constant;

final class LandAreaKind
{
    public const string OPEN_SOIL = 'open_soil';
    public const string SOIL_LESS = 'soil_less';

    public const array ALL = [
        self::OPEN_SOIL,
        self::SOIL_LESS,
    ];
}
