import { describe, expect, it } from 'vitest';

import { HARVEST_PERIOD, getHarvestPeriodRange } from './HarvestPeriod';

// Months are 0-based in Date: new Date(2026, 10, 27) is 27 November 2026, in local time.
describe('getHarvestPeriodRange', () => {
  describe('season', () => {
    it.each([
      [new Date(2026, 10, 27), '2026-03-01'],
      [new Date(2026, 2, 1), '2026-03-01'],
      [new Date(2026, 1, 28), '2025-03-01'],
      [new Date(2026, 0, 1), '2025-03-01'],
      [new Date(2026, 11, 31, 23, 59), '2026-03-01'],
    ])('starts the season of %s on %s, and has no end yet', (now, from) => {
      expect(getHarvestPeriodRange(HARVEST_PERIOD.Season, now)).toEqual({ from });
    });
  });

  it('gives the calendar year', () => {
    expect(getHarvestPeriodRange(HARVEST_PERIOD.Year, new Date(2026, 10, 27))).toEqual({
      from: '2026-01-01',
      to: '2026-12-31',
    });
  });

  it('is open on both sides for "all"', () => {
    expect(getHarvestPeriodRange(HARVEST_PERIOD.All, new Date(2026, 10, 27))).toEqual({});
  });

  it('gives back the bounds of a custom period, and leaves out the ones not set', () => {
    const now = new Date(2026, 10, 27);

    expect(
      getHarvestPeriodRange(HARVEST_PERIOD.Custom, now, { from: '2026-04-01', to: '2026-06-30' }),
    ).toEqual({ from: '2026-04-01', to: '2026-06-30' });
    expect(getHarvestPeriodRange(HARVEST_PERIOD.Custom, now, { from: '2026-04-01' })).toEqual({
      from: '2026-04-01',
    });
    expect(getHarvestPeriodRange(HARVEST_PERIOD.Custom, now, { from: '', to: undefined })).toEqual(
      {},
    );
    expect(getHarvestPeriodRange(HARVEST_PERIOD.Custom, now)).toEqual({});
  });

  it('reads the local date, not the UTC one', () => {
    // 00:30 on 1 March in the local zone is still 28 February in UTC for any zone ahead of it.
    expect(getHarvestPeriodRange(HARVEST_PERIOD.Season, new Date(2026, 2, 1, 0, 30))).toEqual({
      from: '2026-03-01',
    });
  });
});
