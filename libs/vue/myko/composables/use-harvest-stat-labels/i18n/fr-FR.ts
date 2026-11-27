import type { HarvestPeriod } from '@lychen/typescript-front-model-business/mushroom/HarvestPeriod';
import type { HarvestStatGroupBy } from '@lychen/typescript-front-model-business/mushroom/HarvestStat';

// Typed on the model's constants: a grouping or period without its translation is a type error.
const groupBy: Record<HarvestStatGroupBy, string> = {
  mushroomLog: 'Bûche',
  strain: 'Souche',
  fungusSpecies: 'Espèce',
  woodSpecies: 'Essence',
  zone: 'Zone',
  month: 'Mois',
};

const period: Record<HarvestPeriod, string> = {
  season: 'Saison en cours',
  year: 'Année',
  all: 'Tout',
  custom: 'Personnalisée',
};

export default {
  group_by: groupBy,
  period,
};
