import type { ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

/**
 * The wood species Myko's API seeds as reference data (`/api/wood_species`), by `code`, the slug
 * the app translates (`libs/vue/myko`, `useSpeciesLabels`). The API's story seeds exactly these
 * codes; a species added in the database later has no translation and the app shows its
 * scientific name.
 */
export const WOOD_SPECIES_CODE = {
  Oak: 'oak',
  Beech: 'beech',
  Hornbeam: 'hornbeam',
  Chestnut: 'chestnut',
  Birch: 'birch',
  Poplar: 'poplar',
  Maple: 'maple',
  Alder: 'alder',
  Hazel: 'hazel',
  Ash: 'ash',
  Willow: 'willow',
  WildCherry: 'wild-cherry',
} as const;

export type WoodSpeciesCode = ObjectValues<typeof WOOD_SPECIES_CODE>;

export const WOOD_SPECIES_LIST: ReadonlyArray<{ code: WoodSpeciesCode; scientificName: string }> = [
  { code: WOOD_SPECIES_CODE.Oak, scientificName: 'Quercus' },
  { code: WOOD_SPECIES_CODE.Beech, scientificName: 'Fagus' },
  { code: WOOD_SPECIES_CODE.Hornbeam, scientificName: 'Carpinus' },
  { code: WOOD_SPECIES_CODE.Chestnut, scientificName: 'Castanea' },
  { code: WOOD_SPECIES_CODE.Birch, scientificName: 'Betula' },
  { code: WOOD_SPECIES_CODE.Poplar, scientificName: 'Populus' },
  { code: WOOD_SPECIES_CODE.Maple, scientificName: 'Acer' },
  { code: WOOD_SPECIES_CODE.Alder, scientificName: 'Alnus' },
  { code: WOOD_SPECIES_CODE.Hazel, scientificName: 'Corylus' },
  { code: WOOD_SPECIES_CODE.Ash, scientificName: 'Fraxinus' },
  { code: WOOD_SPECIES_CODE.Willow, scientificName: 'Salix' },
  { code: WOOD_SPECIES_CODE.WildCherry, scientificName: 'Prunus avium' },
];
