import type { FungusSpeciesCode } from '@lychen/typescript-myko/constants/FungusSpecies';
import type { WoodSpeciesCode } from '@lychen/typescript-myko/constants/WoodSpecies';

// Typed on the codes: a species without its translation is a type error.
const fungusSpecies: Record<FungusSpeciesCode, string> = {
  shiitake: 'Shiitake',
  oyster: 'Pleurote en huître',
  'lung-oyster': 'Pleurote pulmonaire',
  'black-poplar': 'Pholiote du peuplier',
  'lions-mane': 'Hydne hérisson',
  'wine-cap': 'Strophaire à anneau rugueux',
  nameko: 'Nameko',
  'velvet-shank': 'Collybie à pied velouté',
  reishi: 'Reishi',
  maitake: 'Maitake',
  'jelly-ear': 'Oreille de Judas',
  'sulphur-shelf': 'Polypore soufré',
};

const woodSpecies: Record<WoodSpeciesCode, string> = {
  oak: 'Chêne',
  beech: 'Hêtre',
  hornbeam: 'Charme',
  chestnut: 'Châtaignier',
  birch: 'Bouleau',
  poplar: 'Peuplier',
  maple: 'Érable',
  alder: 'Aulne',
  hazel: 'Noisetier',
  ash: 'Frêne',
  willow: 'Saule',
  'wild-cherry': 'Merisier',
};

export default {
  fungus_species: fungusSpecies,
  wood_species: woodSpecies,
};
