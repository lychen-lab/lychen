import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

/** What the statistics of the harvests are grouped by (`projects/myko/CLAUDE.md`, "API conventions"). */
export const HARVEST_STAT_GROUP_BY = {
  MushroomLog: 'mushroomLog',
  Strain: 'strain',
  FungusSpecies: 'fungusSpecies',
  WoodSpecies: 'woodSpecies',
  Zone: 'zone',
  Month: 'month',
} as const;

export type HarvestStatGroupBy = ObjectValues<typeof HARVEST_STAT_GROUP_BY>;

/**
 * What a group of harvests weighed: one log, one strain, one species, one zone or one month,
 * whatever the statistics are grouped by. Every field is optional: an endpoint says which ones
 * it gives, a component which ones it needs (`WithRequired`).
 */
export interface HarvestStat {
  /** What identifies the group: the id of a log, a strain…, or a month such as `2026-03`. */
  id?: string;
  /** What the group is called, such as a log's label or a strain's name. */
  label?: string;
  totalGrams?: number;
  harvestCount?: number;
}
