import { describe, expect, it } from 'vitest';

import { MUSHROOM_LOG_STATE } from './MushroomLog';
import {
  countMushroomLogListFilters,
  hasMushroomLogListFilters,
  MUSHROOM_LOG_LIST_QUERY_KEY,
  parseMushroomLogListQuery,
  toMushroomLogListQuery,
  withMushroomLogListFilters,
  type MushroomLogListFilters,
} from './MushroomLogListFilters';

describe('parseMushroomLogListQuery', () => {
  it('reads every filter from the query', () => {
    expect(
      parseMushroomLogListQuery({
        label: 'B-01',
        zone: 'zone-1',
        state: 'incubating',
        fungusSpecies: 'shiitake-id',
        woodSpecies: 'oak-id',
      }),
    ).toEqual({
      label: 'B-01',
      zone: 'zone-1',
      state: MUSHROOM_LOG_STATE.Incubating,
      fungusSpecies: 'shiitake-id',
      woodSpecies: 'oak-id',
    });
  });

  it('gives no filter for an empty query', () => {
    expect(parseMushroomLogListQuery({})).toEqual({});
  });

  it.each(Object.values(MUSHROOM_LOG_STATE))('knows the state %s', (state) => {
    expect(parseMushroomLogListQuery({ state })).toEqual({ state });
  });

  it('ignores a state this front does not know, and keeps the other filters', () => {
    expect(parseMushroomLogListQuery({ state: 'dormant', zone: 'zone-1' })).toEqual({
      zone: 'zone-1',
    });
  });

  it('trims what it reads and treats blank as absent', () => {
    expect(
      parseMushroomLogListQuery({
        label: '  B-01 ',
        zone: '   ',
        fungusSpecies: '',
        woodSpecies: null,
      }),
    ).toEqual({ label: 'B-01' });
  });

  it('takes the first value that says something when a parameter is given twice', () => {
    expect(parseMushroomLogListQuery({ zone: ['', 'zone-2', 'zone-3'] })).toEqual({
      zone: 'zone-2',
    });
    expect(parseMushroomLogListQuery({ zone: [null, undefined] })).toEqual({});
    expect(parseMushroomLogListQuery({ state: ['dormant', 'fruiting'] })).toEqual({});
  });

  it('ignores the parameters that are not filters', () => {
    expect(parseMushroomLogListQuery({ page: '2', redirect: '/logs' })).toEqual({});
  });
});

describe('toMushroomLogListQuery', () => {
  it('says only the filters that are set', () => {
    expect(
      toMushroomLogListQuery({
        label: ' B-01 ',
        zone: 'zone-1',
        state: MUSHROOM_LOG_STATE.Fruiting,
        woodSpecies: 'oak-id',
      }),
    ).toEqual({ label: 'B-01', zone: 'zone-1', state: 'fruiting', woodSpecies: 'oak-id' });
  });

  it('is empty for the unfiltered list, and leaves out blank and undefined filters', () => {
    expect(toMushroomLogListQuery({})).toEqual({});
    expect(toMushroomLogListQuery({ label: '   ', zone: '', state: undefined })).toEqual({});
  });

  it('is the inverse of parseMushroomLogListQuery', () => {
    const filters: MushroomLogListFilters = {
      label: 'B-0',
      zone: 'zone-1',
      state: MUSHROOM_LOG_STATE.Prepared,
      fungusSpecies: 'shiitake-id',
      woodSpecies: 'oak-id',
    };

    expect(parseMushroomLogListQuery(toMushroomLogListQuery(filters))).toEqual(filters);
    expect(toMushroomLogListQuery(parseMushroomLogListQuery({ zone: 'zone-1' }))).toEqual({
      zone: 'zone-1',
    });
  });

  it('uses the names of MUSHROOM_LOG_LIST_QUERY_KEY', () => {
    expect(
      Object.keys(
        toMushroomLogListQuery({
          label: 'a',
          zone: 'b',
          state: 'retired',
          fungusSpecies: 'c',
          woodSpecies: 'd',
        }),
      ).sort(),
    ).toEqual(Object.values(MUSHROOM_LOG_LIST_QUERY_KEY).sort());
  });
});

describe('withMushroomLogListFilters', () => {
  it('replaces the filters of a query and keeps the other parameters', () => {
    expect(
      withMushroomLogListFilters(
        { zone: 'zone-1', state: 'prepared', label: 'B-1', redirect: '/logs' },
        { zone: 'zone-2' },
      ),
    ).toEqual({ redirect: '/logs', zone: 'zone-2' });
  });

  it('leaves no stale filter behind when the filters are cleared', () => {
    expect(
      withMushroomLogListFilters({ zone: ['a', 'b'], state: 'fruiting', page: '3' }, {}),
    ).toEqual({
      page: '3',
    });
  });

  it('does not change the query it is given', () => {
    const query = { zone: 'zone-1' };

    withMushroomLogListFilters(query, { state: MUSHROOM_LOG_STATE.Retired });

    expect(query).toEqual({ zone: 'zone-1' });
  });
});

describe('countMushroomLogListFilters', () => {
  it('counts the filters of the sheet', () => {
    expect(countMushroomLogListFilters({})).toBe(0);
    expect(countMushroomLogListFilters({ zone: 'zone-1' })).toBe(1);
    expect(
      countMushroomLogListFilters({
        zone: 'zone-1',
        state: MUSHROOM_LOG_STATE.Fruiting,
        fungusSpecies: 'shiitake-id',
        woodSpecies: 'oak-id',
      }),
    ).toBe(4);
  });

  it('does not count the label search, which has its own field', () => {
    expect(countMushroomLogListFilters({ label: 'B-01' })).toBe(0);
  });
});

describe('hasMushroomLogListFilters', () => {
  it('is false for the unfiltered list', () => {
    expect(hasMushroomLogListFilters({})).toBe(false);
    expect(hasMushroomLogListFilters({ label: '   ' })).toBe(false);
  });

  it('is true for any filter, the label search included', () => {
    expect(hasMushroomLogListFilters({ label: 'B-01' })).toBe(true);
    expect(hasMushroomLogListFilters({ state: MUSHROOM_LOG_STATE.Prepared })).toBe(true);
    expect(hasMushroomLogListFilters({ woodSpecies: 'oak-id' })).toBe(true);
  });
});
