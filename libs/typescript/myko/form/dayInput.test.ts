import { describe, expect, it } from 'vitest';

import {
  fromDateTimeInputValue,
  fromDayInputValue,
  toDateTimeInputValue,
  toDayInputValue,
} from './dayInput';

describe('toDayInputValue', () => {
  it('writes the local day, padded', () => {
    expect(toDayInputValue(new Date(2026, 2, 5, 23, 59))).toBe('2026-03-05');
    expect(toDayInputValue(new Date(2026, 11, 31, 0, 0))).toBe('2026-12-31');
  });

  it('pads a year before 1000 to four digits, as the field wants it', () => {
    expect(toDayInputValue(new Date(2026, 0, 1))).toMatch(/^\d{4}-01-01$/);
  });
});

describe('toDateTimeInputValue', () => {
  it('writes the local day and time without seconds, padded', () => {
    expect(toDateTimeInputValue(new Date(2026, 2, 5, 8, 5, 59))).toBe('2026-03-05T08:05');
    expect(toDateTimeInputValue(new Date(2026, 2, 5, 23, 59))).toBe('2026-03-05T23:59');
  });
});

describe('fromDayInputValue', () => {
  it('reads a day as the local midnight', () => {
    const date = fromDayInputValue('2026-03-12');

    expect(date).toEqual(new Date(2026, 2, 12, 0, 0));
  });

  it('gives undefined for an empty value, a malformed one and an impossible day', () => {
    expect(fromDayInputValue('')).toBeUndefined();
    expect(fromDayInputValue('12/03/2026')).toBeUndefined();
    expect(fromDayInputValue('2026-3-12')).toBeUndefined();
    expect(fromDayInputValue('2026-02-31')).toBeUndefined();
    expect(fromDayInputValue('2026-13-01')).toBeUndefined();
    expect(fromDayInputValue('2026-00-10')).toBeUndefined();
  });

  it('accepts the 29th of February of a leap year only', () => {
    expect(fromDayInputValue('2028-02-29')).toEqual(new Date(2028, 1, 29));
    expect(fromDayInputValue('2026-02-29')).toBeUndefined();
  });

  it('round trips with toDayInputValue', () => {
    const day = new Date(2026, 10, 28, 15, 42);

    expect(fromDayInputValue(toDayInputValue(day))).toEqual(new Date(2026, 10, 28));
  });
});

describe('fromDateTimeInputValue', () => {
  it('reads a moment as local time', () => {
    expect(fromDateTimeInputValue('2026-03-12T08:05')).toEqual(new Date(2026, 2, 12, 8, 5));
  });

  it('gives undefined for an empty value, a missing time and an impossible time', () => {
    expect(fromDateTimeInputValue('')).toBeUndefined();
    expect(fromDateTimeInputValue('2026-03-12')).toBeUndefined();
    expect(fromDateTimeInputValue('2026-03-12T24:00')).toBeUndefined();
    expect(fromDateTimeInputValue('2026-03-12T08:60')).toBeUndefined();
    expect(fromDateTimeInputValue('2026-02-30T08:05')).toBeUndefined();
  });

  it('round trips with toDateTimeInputValue', () => {
    const moment = new Date(2026, 10, 28, 5, 7);

    expect(fromDateTimeInputValue(toDateTimeInputValue(moment))).toEqual(moment);
  });
});
