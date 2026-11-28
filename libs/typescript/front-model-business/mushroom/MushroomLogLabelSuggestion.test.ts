import { describe, expect, it } from 'vitest';

import { suggestMushroomLogLabel } from './MushroomLogLabelSuggestion';

describe('suggestMushroomLogLabel', () => {
  it('suggests nothing without labels, or without a label that ends with a number', () => {
    expect(suggestMushroomLogLabel([])).toBeUndefined();
    expect(suggestMushroomLogLabel(['Chêne', 'Grosse bûche', 'B-12 fendue'])).toBeUndefined();
  });

  it('suggests the number after the highest, written with the same digits', () => {
    expect(suggestMushroomLogLabel(['B-001', 'B-002', 'B-040'])).toEqual({
      prefix: 'B-',
      startNumber: 41,
      digits: 3,
      label: 'B-041',
    });
  });

  it('does not fill the gaps, whatever the order of the labels', () => {
    expect(suggestMushroomLogLabel(['B-007', 'B-002', 'B-005'])?.label).toBe('B-008');
  });

  it('grows the number past its digits, and keeps the width of one that is not padded', () => {
    expect(suggestMushroomLogLabel(['B-098', 'B-099'])).toMatchObject({
      startNumber: 100,
      digits: 3,
      label: 'B-100',
    });
    expect(suggestMushroomLogLabel(['B-8', 'B-9'])).toMatchObject({
      startNumber: 10,
      digits: 1,
      label: 'B-10',
    });
  });

  it('works without a prefix, and with digits inside the prefix', () => {
    expect(suggestMushroomLogLabel(['001', '002'])).toMatchObject({ prefix: '', label: '003' });
    expect(suggestMushroomLogLabel(['A1-007', 'A1-008'])).toMatchObject({
      prefix: 'A1-',
      label: 'A1-009',
    });
  });

  it('follows the prefix most labels use', () => {
    expect(suggestMushroomLogLabel(['T-100', 'B-001', 'B-002', 'B-003'])?.label).toBe('B-004');
  });

  it('breaks a tie towards the prefix that goes furthest, then alphabetically', () => {
    expect(suggestMushroomLogLabel(['B-010', 'S-020'])?.label).toBe('S-021');
    expect(suggestMushroomLogLabel(['S-010', 'B-010'])?.label).toBe('B-011');
    expect(suggestMushroomLogLabel(['B-010', 'S-010'])?.label).toBe('B-011');
  });

  it('ignores the spaces around a label and a number too large to add 1 to', () => {
    expect(suggestMushroomLogLabel(['  B-001 ', 'B-002  '])?.label).toBe('B-003');
    expect(suggestMushroomLogLabel(['X-99999999999999999999'])).toBeUndefined();
    expect(suggestMushroomLogLabel([`X-${Number.MAX_SAFE_INTEGER}`, 'B-001'])?.label).toBe('B-002');
  });

  it('keeps the widest writing of the highest number', () => {
    expect(suggestMushroomLogLabel(['B-5', 'B-05', 'B-4'])).toMatchObject({
      digits: 2,
      label: 'B-06',
    });
  });
});
