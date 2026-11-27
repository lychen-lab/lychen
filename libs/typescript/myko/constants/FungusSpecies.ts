import type { ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

/**
 * The fungus species Myko's API seeds as reference data (`/api/fungus_species`), by `code`, the
 * slug the app translates (`libs/vue/myko`, `useSpeciesLabels`). The API's story seeds exactly
 * these codes; a species added in the database later has no translation and the app shows its
 * scientific name.
 */
export const FUNGUS_SPECIES_CODE = {
  Shiitake: 'shiitake',
  Oyster: 'oyster',
  LungOyster: 'lung-oyster',
  BlackPoplar: 'black-poplar',
  LionsMane: 'lions-mane',
  WineCap: 'wine-cap',
  Nameko: 'nameko',
  VelvetShank: 'velvet-shank',
  Reishi: 'reishi',
  Maitake: 'maitake',
  JellyEar: 'jelly-ear',
  SulphurShelf: 'sulphur-shelf',
} as const;

export type FungusSpeciesCode = ObjectValues<typeof FUNGUS_SPECIES_CODE>;

export const FUNGUS_SPECIES_LIST: ReadonlyArray<{
  code: FungusSpeciesCode;
  scientificName: string;
}> = [
  { code: FUNGUS_SPECIES_CODE.Shiitake, scientificName: 'Lentinula edodes' },
  { code: FUNGUS_SPECIES_CODE.Oyster, scientificName: 'Pleurotus ostreatus' },
  { code: FUNGUS_SPECIES_CODE.LungOyster, scientificName: 'Pleurotus pulmonarius' },
  { code: FUNGUS_SPECIES_CODE.BlackPoplar, scientificName: 'Cyclocybe aegerita' },
  { code: FUNGUS_SPECIES_CODE.LionsMane, scientificName: 'Hericium erinaceus' },
  { code: FUNGUS_SPECIES_CODE.WineCap, scientificName: 'Stropharia rugosoannulata' },
  { code: FUNGUS_SPECIES_CODE.Nameko, scientificName: 'Pholiota microspora' },
  { code: FUNGUS_SPECIES_CODE.VelvetShank, scientificName: 'Flammulina velutipes' },
  { code: FUNGUS_SPECIES_CODE.Reishi, scientificName: 'Ganoderma lucidum' },
  { code: FUNGUS_SPECIES_CODE.Maitake, scientificName: 'Grifola frondosa' },
  { code: FUNGUS_SPECIES_CODE.JellyEar, scientificName: 'Auricularia auricula-judae' },
  { code: FUNGUS_SPECIES_CODE.SulphurShelf, scientificName: 'Laetiporus sulphureus' },
];
