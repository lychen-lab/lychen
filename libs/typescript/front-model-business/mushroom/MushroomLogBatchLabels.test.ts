import { describe, expect, it } from 'vitest';

import {
  MUSHROOM_LOG_BATCH_MAX_COUNT,
  MUSHROOM_LOG_BATCH_MAX_DIGITS,
  isValidMushroomLogBatchNumbering,
  mushroomLogBatchLabels,
} from './MushroomLogBatchLabels';

describe('mushroomLogBatchLabels', () => {
  it('makes the spec’s example: B-001 to B-040', () => {
    const labels = mushroomLogBatchLabels({ prefix: 'B-', startNumber: 1, count: 40, digits: 3 });

    expect(labels).toHaveLength(40);
    expect(labels[0]).toBe('B-001');
    expect(labels[8]).toBe('B-009');
    expect(labels[9]).toBe('B-010');
    expect(labels[39]).toBe('B-040');
  });

  it('starts anywhere, to go on after an earlier batch', () => {
    expect(mushroomLogBatchLabels({ prefix: 'B-', startNumber: 41, count: 3, digits: 3 })).toEqual([
      'B-041',
      'B-042',
      'B-043',
    ]);
  });

  it('never cuts a number longer than the digits', () => {
    expect(mushroomLogBatchLabels({ prefix: 'B-', startNumber: 998, count: 4, digits: 3 })).toEqual(
      ['B-998', 'B-999', 'B-1000', 'B-1001'],
    );
  });

  it('pads nothing with one digit, and accepts a start of zero', () => {
    expect(mushroomLogBatchLabels({ prefix: 'T', startNumber: 0, count: 3, digits: 1 })).toEqual([
      'T0',
      'T1',
      'T2',
    ]);
  });

  it('keeps the prefix as typed, empty or with spaces', () => {
    expect(mushroomLogBatchLabels({ prefix: '', startNumber: 1, count: 2, digits: 2 })).toEqual([
      '01',
      '02',
    ]);
    expect(
      mushroomLogBatchLabels({ prefix: 'Chêne ', startNumber: 1, count: 1, digits: 2 }),
    ).toEqual(['Chêne 01']);
  });

  it('gives each label once', () => {
    const labels = mushroomLogBatchLabels({ prefix: 'B-', startNumber: 1, count: 200, digits: 3 });

    expect(new Set(labels).size).toBe(200);
  });

  it.each([
    ['no log', { startNumber: 1, count: 0, digits: 3 }],
    ['a negative count', { startNumber: 1, count: -2, digits: 3 }],
    ['a fractional count', { startNumber: 1, count: 2.5, digits: 3 }],
    ['too many logs', { startNumber: 1, count: MUSHROOM_LOG_BATCH_MAX_COUNT + 1, digits: 3 }],
    ['a negative start', { startNumber: -1, count: 3, digits: 3 }],
    ['a fractional start', { startNumber: 1.5, count: 3, digits: 3 }],
    ['no digit', { startNumber: 1, count: 3, digits: 0 }],
    ['too many digits', { startNumber: 1, count: 3, digits: MUSHROOM_LOG_BATCH_MAX_DIGITS + 1 }],
    ['a number that is not one', { startNumber: Number.NaN, count: 3, digits: 3 }],
    ['an infinite count', { startNumber: 1, count: Number.POSITIVE_INFINITY, digits: 3 }],
  ])('makes no label for %s, so that a form shows no preview yet', (_, numbering) => {
    expect(mushroomLogBatchLabels({ prefix: 'B-', ...numbering })).toEqual([]);
  });
});

describe('isValidMushroomLogBatchNumbering', () => {
  it('accepts the limits themselves', () => {
    expect(
      isValidMushroomLogBatchNumbering({
        prefix: '',
        startNumber: 0,
        count: MUSHROOM_LOG_BATCH_MAX_COUNT,
        digits: MUSHROOM_LOG_BATCH_MAX_DIGITS,
      }),
    ).toBe(true);
    expect(
      isValidMushroomLogBatchNumbering({ prefix: 'B-', startNumber: 1, count: 1, digits: 1 }),
    ).toBe(true);
  });

  it('rejects what makes no label', () => {
    expect(
      isValidMushroomLogBatchNumbering({ prefix: 'B-', startNumber: 1, count: 0, digits: 3 }),
    ).toBe(false);
  });
});
