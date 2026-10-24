<?php

namespace App\Constant;

final class UsdaZone
{
    public const string ZONE_0A = 'zone_0a';
    public const string ZONE_0B = 'zone_0b';
    public const string ZONE_1A = 'zone_1a';
    public const string ZONE_1B = 'zone_1b';
    public const string ZONE_2A = 'zone_2a';
    public const string ZONE_2B = 'zone_2b';
    public const string ZONE_3A = 'zone_3a';
    public const string ZONE_3B = 'zone_3b';
    public const string ZONE_4A = 'zone_4a';
    public const string ZONE_4B = 'zone_4b';
    public const string ZONE_5A = 'zone_5a';
    public const string ZONE_5B = 'zone_5b';
    public const string ZONE_6A = 'zone_6a';
    public const string ZONE_6B = 'zone_6b';
    public const string ZONE_7A = 'zone_7a';
    public const string ZONE_7B = 'zone_7b';
    public const string ZONE_8A = 'zone_8a';
    public const string ZONE_8B = 'zone_8b';
    public const string ZONE_9A = 'zone_9a';
    public const string ZONE_9B = 'zone_9b';
    public const string ZONE_10A = 'zone_10a';
    public const string ZONE_10B = 'zone_10b';
    public const string ZONE_11A = 'zone_11a';
    public const string ZONE_11B = 'zone_11b';
    public const string ZONE_12A = 'zone_12a';
    public const string ZONE_12B = 'zone_12b';
    public const string ZONE_13A = 'zone_13a';
    public const string ZONE_13B = 'zone_13b';
    public const string ZONE_14A = 'zone_14a';

    public const array ALL = [
        self::ZONE_0A,
        self::ZONE_0B,
        self::ZONE_1A,
        self::ZONE_1B,
        self::ZONE_2A,
        self::ZONE_2B,
        self::ZONE_3A,
        self::ZONE_3B,
        self::ZONE_4A,
        self::ZONE_4B,
        self::ZONE_5A,
        self::ZONE_5B,
        self::ZONE_6A,
        self::ZONE_6B,
        self::ZONE_7A,
        self::ZONE_7B,
        self::ZONE_8A,
        self::ZONE_8B,
        self::ZONE_9A,
        self::ZONE_9B,
        self::ZONE_10A,
        self::ZONE_10B,
        self::ZONE_11A,
        self::ZONE_11B,
        self::ZONE_12A,
        self::ZONE_12B,
        self::ZONE_13A,
        self::ZONE_13B,
        self::ZONE_14A,
    ];

    public const array TEMPERATURE_RANGES = [
        self::ZONE_0A => ['min' => null, 'max' => -53.9],
        self::ZONE_0B => ['min' => -53.9, 'max' => -51.1],
        self::ZONE_1A => ['min' => -51.1, 'max' => -48.3],
        self::ZONE_1B => ['min' => -48.3, 'max' => -45.6],
        self::ZONE_2A => ['min' => -45.6, 'max' => -42.8],
        self::ZONE_2B => ['min' => -42.8, 'max' => -40.0],
        self::ZONE_3A => ['min' => -40.0, 'max' => -37.2],
        self::ZONE_3B => ['min' => -37.2, 'max' => -34.4],
        self::ZONE_4A => ['min' => -34.4, 'max' => -31.7],
        self::ZONE_4B => ['min' => -31.7, 'max' => -28.9],
        self::ZONE_5A => ['min' => -28.9, 'max' => -26.1],
        self::ZONE_5B => ['min' => -26.1, 'max' => -23.3],
        self::ZONE_6A => ['min' => -23.3, 'max' => -20.6],
        self::ZONE_6B => ['min' => -20.6, 'max' => -17.8],
        self::ZONE_7A => ['min' => -17.8, 'max' => -15.0],
        self::ZONE_7B => ['min' => -15.0, 'max' => -12.2],
        self::ZONE_8A => ['min' => -12.2, 'max' => -9.4],
        self::ZONE_8B => ['min' => -9.4, 'max' => -6.7],
        self::ZONE_9A => ['min' => -6.7, 'max' => -3.9],
        self::ZONE_9B => ['min' => -3.9, 'max' => -1.1],
        self::ZONE_10A => ['min' => -1.1, 'max' => 1.7],
        self::ZONE_10B => ['min' => 1.7, 'max' => 4.4],
        self::ZONE_11A => ['min' => 4.4, 'max' => 7.2],
        self::ZONE_11B => ['min' => 7.2, 'max' => 10.0],
        self::ZONE_12A => ['min' => 10.0, 'max' => 12.8],
        self::ZONE_12B => ['min' => 12.8, 'max' => 15.6],
        self::ZONE_13A => ['min' => 15.6, 'max' => 18.3],
        self::ZONE_13B => ['min' => 18.3, 'max' => 21.1],
        self::ZONE_14A => ['min' => 21.1, 'max' => null],
    ];
}
