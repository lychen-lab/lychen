import type { FungusSpeciesCode } from '@lychen/typescript-myko/constants/FungusSpecies';
import type { WoodSpeciesCode } from '@lychen/typescript-myko/constants/WoodSpecies';

// Typed on the codes: a species without its translation is a type error.
const fungusSpecies: Record<FungusSpeciesCode, string> = {
  shiitake: 'Shiitake',
  oyster: 'Oyster mushroom',
  'lung-oyster': 'Lung oyster mushroom',
  'black-poplar': 'Black poplar mushroom',
  'lions-mane': 'Lion’s mane',
  'wine-cap': 'Wine cap',
  nameko: 'Nameko',
  'velvet-shank': 'Velvet shank',
  reishi: 'Reishi',
  maitake: 'Maitake',
  'jelly-ear': 'Jelly ear',
  'sulphur-shelf': 'Sulphur shelf',
};

const woodSpecies: Record<WoodSpeciesCode, string> = {
  oak: 'Oak',
  beech: 'Beech',
  hornbeam: 'Hornbeam',
  chestnut: 'Chestnut',
  birch: 'Birch',
  poplar: 'Poplar',
  maple: 'Maple',
  alder: 'Alder',
  hazel: 'Hazel',
  ash: 'Ash',
  willow: 'Willow',
  'wild-cherry': 'Wild cherry',
};

export default {
  fungus_species: fungusSpecies,
  wood_species: woodSpecies,
};
