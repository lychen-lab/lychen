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
