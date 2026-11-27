import type { HarvestPeriod } from '@lychen/typescript-front-model-business/mushroom/HarvestPeriod';
import type { HarvestStatGroupBy } from '@lychen/typescript-front-model-business/mushroom/HarvestStat';

// Typed on the model's constants: a grouping or period without its translation is a type error.
const groupBy: Record<HarvestStatGroupBy, string> = {
  mushroomLog: 'Log',
  strain: 'Strain',
  fungusSpecies: 'Species',
  woodSpecies: 'Wood',
  zone: 'Zone',
  month: 'Month',
};

const period: Record<HarvestPeriod, string> = {
  season: 'Current season',
  year: 'Year',
  all: 'All time',
  custom: 'Custom',
};

export default {
  group_by: groupBy,
  period,
};
