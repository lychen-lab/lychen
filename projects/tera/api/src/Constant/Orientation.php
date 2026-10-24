<?php

namespace App\Constant;

final class Orientation
{
    public const string NORTH = 'north';
    public const string NORTH_EAST = 'north_east';
    public const string EAST = 'east';
    public const string SOUTH_EAST = 'south_east';
    public const string SOUTH = 'south';
    public const string SOUTH_WEST = 'south_west';
    public const string WEST = 'west';
    public const string NORTH_WEST = 'north_west';

    public const array ALL = [
        self::NORTH,
        self::NORTH_EAST,
        self::EAST,
        self::SOUTH_EAST,
        self::SOUTH,
        self::SOUTH_WEST,
        self::WEST,
        self::NORTH_WEST,
    ];
}
