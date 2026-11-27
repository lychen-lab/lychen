import { describe, expect, it } from 'vitest';

import { formatHarvestWeight } from './formatHarvestWeight';

// Intl groups thousands with a narrow no-break space in French.
const NNBSP = '\u202f';

describe('formatHarvestWeight', () => {
  it.each([
    [0, '0 g'],
    [1, '1 g'],
    [850, '850 g'],
    [999, '999 g'],
  ])('gives %i g in grams, below a kilo', (grams, expected) => {
    expect(formatHarvestWeight(grams, 'fr-FR')).toBe(expected);
  });

  it.each([
    [1000, '1,0 kg'],
    [1234, '1,2 kg'],
    [1250, '1,3 kg'],
    [12000, '12,0 kg'],
    [12345, '12,3 kg'],
    [1234567, `1${NNBSP}234,6 kg`],
  ])('gives %i g in kilos with one decimal, in French', (grams, expected) => {
    expect(formatHarvestWeight(grams, 'fr-FR')).toBe(expected);
  });

  it('follows the language: a decimal point in English', () => {
    expect(formatHarvestWeight(1234, 'en-US')).toBe('1.2 kg');
    expect(formatHarvestWeight(1234567, 'en-US')).toBe('1,234.6 kg');
    expect(formatHarvestWeight(850, 'en-US')).toBe('850 g');
  });

  it('rounds grams to a whole number', () => {
    expect(formatHarvestWeight(850.4, 'fr-FR')).toBe('850 g');
  });

  it.each([undefined, Number.NaN, Number.POSITIVE_INFINITY])('gives nothing for %s', (grams) => {
    expect(formatHarvestWeight(grams, 'fr-FR')).toBe('');
  });
});
