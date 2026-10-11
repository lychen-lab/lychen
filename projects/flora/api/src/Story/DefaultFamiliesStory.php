<?php

namespace App\Story;

use App\Factory\FamilyFactory;
use Zenstruck\Foundry\Story;

final class DefaultFamiliesStory extends Story
{
    public const string ASTERACEAE = 'asteraceae';
    public const string BRASSICACEAE = 'brassicaceae';
    public const string FABACEAE = 'fabaceae';
    public const string SOLANACEAE = 'solanaceae';
    public const string CUCURBITACEAE = 'cucurbitaceae';
    public const string LILIACEAE = 'liliaceae';
    public const string POACEAE = 'poaceae';
    public const string APIACEAE = 'apiaceae';
    public const string LAMIACEAE = 'lamiaceae';

    public const array ALL = [
        self::ASTERACEAE,
        self::BRASSICACEAE,
        self::FABACEAE,
        self::SOLANACEAE,
        self::CUCURBITACEAE,
        self::LILIACEAE,
        self::POACEAE,
        self::APIACEAE,
        self::LAMIACEAE,
    ];

    public function build(): void
    {
        foreach (self::ALL as $code) {
            $this->addState($code, FamilyFactory::new(['code' => $code])->create());
        }
    }
}
