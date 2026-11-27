import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

/** The periods the statistics of the harvests are shown over: the season is the default. */
export const HARVEST_PERIOD = {
  Season: 'season',
  Year: 'year',
  All: 'all',
  Custom: 'custom',
} as const;

export type HarvestPeriod = ObjectValues<typeof HARVEST_PERIOD>;

/**
 * Where a season starts: 1 March, when the logs wake up in spring. A season runs from there to the
 * next 1 March, so that January and February still belong to the previous one. A product choice
 * (the spec only says "la saison en cours"): change it here, nowhere else.
 */
export const SEASON_START_MONTH = 3;

/**
 * Dates as the API takes them (`from` and `to` of `harvest_stats` and `harvests`): `YYYY-MM-DD`,
 * each one included. A bound left out is open.
 */
export interface HarvestPeriodRange {
  from?: string;
  to?: string;
}

/**
 * The dates of a period, seen from `now` in the local time zone. The current season has no end
 * (it ends in the future), and "all" has neither bound. A custom period gives back its own bounds.
 */
export function getHarvestPeriodRange(
  period: HarvestPeriod,
  now: Date,
  custom: HarvestPeriodRange = {},
): HarvestPeriodRange {
  switch (period) {
    case HARVEST_PERIOD.Season: {
      const startYear =
        now.getMonth() + 1 >= SEASON_START_MONTH ? now.getFullYear() : now.getFullYear() - 1;
      return { from: toIsoDate(startYear, SEASON_START_MONTH, 1) };
    }
    case HARVEST_PERIOD.Year:
      return {
        from: toIsoDate(now.getFullYear(), 1, 1),
        to: toIsoDate(now.getFullYear(), 12, 31),
      };
    case HARVEST_PERIOD.Custom:
      return {
        ...(custom.from ? { from: custom.from } : {}),
        ...(custom.to ? { to: custom.to } : {}),
      };
    case HARVEST_PERIOD.All:
      return {};
  }
}

function toIsoDate(year: number, month: number, day: number): string {
  return [
    String(year).padStart(4, '0'),
    String(month).padStart(2, '0'),
    String(day).padStart(2, '0'),
  ].join('-');
}
