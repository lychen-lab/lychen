<?php

namespace App\Story;

use App\Factory\SpeciesFactory;
use Zenstruck\Foundry\Story;

final class DefaultSpeciesStory extends Story
{
    public const string LACTUCA_SATIVA = 'lactuca_sativa';
    public const string BRASSICA_OLERACEA = 'brassica_oleracea';
    public const string SOLANUM_LYCOPERSICUM = 'solanum_lycopersicum';
    public const string DAUCUS_CAROTA = 'daucus_carota';
    public const string PHASEOLUS_VULGARIS = 'phaseolus_vulgaris';
    public const string CUCUMIS_SATIVUS = 'cucumis_sativus';
    public const string CUCUMIS_METULIFERUS = 'cucumis_metuliferus';
    public const string CYNARA_CARDUNCIUS = 'cynara_cardunculus';
    public const string OCIMUM_BASILICUM = 'ocimum_basiliatum';
    public const string CENTAUREA_CYANUS = 'centaurea_cyanus';

    public const array ALL = [
        self::LACTUCA_SATIVA,
        self::BRASSICA_OLERACEA,
        self::SOLANUM_LYCOPERSICUM,
        self::DAUCUS_CAROTA,
        self::PHASEOLUS_VULGARIS,
        self::CUCUMIS_SATIVUS,
        self::CUCUMIS_METULIFERUS,
        self::CYNARA_CARDUNCIUS,
        self::OCIMUM_BASILICUM,
        self::CENTAUREA_CYANUS,
    ];

    public function build(): void
    {
        $this->createSpecies(self::DAUCUS_CAROTA, DefaultFamiliesStory::APIACEAE);
        $this->createSpecies(self::LACTUCA_SATIVA, DefaultFamiliesStory::ASTERACEAE);
        $this->createSpecies(self::SOLANUM_LYCOPERSICUM, DefaultFamiliesStory::SOLANACEAE);
        $this->createSpecies(self::BRASSICA_OLERACEA, DefaultFamiliesStory::BRASSICACEAE);
        $this->createSpecies(self::PHASEOLUS_VULGARIS, DefaultFamiliesStory::FABACEAE);
        $this->createSpecies(self::CUCUMIS_SATIVUS, DefaultFamiliesStory::CUCURBITACEAE);
        $this->createSpecies(self::CUCUMIS_METULIFERUS, DefaultFamiliesStory::CUCURBITACEAE);
        $this->createSpecies(self::CYNARA_CARDUNCIUS, DefaultFamiliesStory::ASTERACEAE);
        $this->createSpecies(self::OCIMUM_BASILICUM, DefaultFamiliesStory::LAMIACEAE);
        $this->createSpecies(self::CENTAUREA_CYANUS, DefaultFamiliesStory::ASTERACEAE);
    }

    protected function createSpecies(string $code, string $familyCode): void
    {
        $this->addState(
            $code,
            SpeciesFactory::new([
                'code' => $code,
                'family' => DefaultFamiliesStory::get($familyCode),
            ])->create()
        );
    }
}
