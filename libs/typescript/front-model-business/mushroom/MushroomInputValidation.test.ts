import { describe, expect, it } from 'vitest';

import { JOURNAL_ENTRY_KIND } from './JournalEntry';
import {
  INPUT_ISSUE_CODE,
  ZONE_NAME_MAX_LENGTH,
  validateHarvestInput,
  validateInoculationInput,
  validateJournalEntryInput,
  validateMushroomLogInput,
  validateZoneInput,
} from './MushroomInputValidation';

const { Required, TooLong, NotPositiveInteger, InFuture } = INPUT_ISSUE_CODE;

// 12 March 2026, 14:30, local time.
const NOW = new Date(2026, 2, 12, 14, 30);

describe('validateZoneInput', () => {
  it('accepts a name', () => {
    expect(validateZoneInput({ name: 'Sous-bois' })).toEqual([]);
  });

  it('requires a name, and a blank one is none', () => {
    expect(validateZoneInput({})).toEqual([{ field: 'name', code: Required }]);
    expect(validateZoneInput({ name: '   ' })).toEqual([{ field: 'name', code: Required }]);
  });

  it('accepts a name of 255 characters and refuses one of 256', () => {
    expect(validateZoneInput({ name: 'a'.repeat(ZONE_NAME_MAX_LENGTH) })).toEqual([]);
    expect(validateZoneInput({ name: 'a'.repeat(ZONE_NAME_MAX_LENGTH + 1) })).toEqual([
      { field: 'name', code: TooLong },
    ]);
  });
});

describe('validateMushroomLogInput', () => {
  it('accepts a label alone, and a label with whole dimensions', () => {
    expect(validateMushroomLogInput({ label: 'B-012' })).toEqual([]);
    expect(validateMushroomLogInput({ label: 'B-012', diameterCm: 12, lengthCm: 100 })).toEqual([]);
  });

  it('requires a label', () => {
    expect(validateMushroomLogInput({ label: ' ' })).toEqual([{ field: 'label', code: Required }]);
  });

  it('refuses a dimension that is zero, negative or not whole', () => {
    expect(validateMushroomLogInput({ label: 'B-1', diameterCm: 0, lengthCm: -3 })).toEqual([
      { field: 'diameterCm', code: NotPositiveInteger },
      { field: 'lengthCm', code: NotPositiveInteger },
    ]);
    expect(validateMushroomLogInput({ label: 'B-1', lengthCm: 80.5 })).toEqual([
      { field: 'lengthCm', code: NotPositiveInteger },
    ]);
  });
});

describe('validateHarvestInput', () => {
  it('accepts today and a day in the past, with a weight in grams', () => {
    expect(validateHarvestInput({ harvestedAt: NOW, weightGrams: 850 }, NOW)).toEqual([]);
    expect(
      validateHarvestInput({ harvestedAt: new Date(2025, 9, 1), weightGrams: 1 }, NOW),
    ).toEqual([]);
  });

  it('accepts the end of today, whatever the time of day', () => {
    expect(
      validateHarvestInput({ harvestedAt: new Date(2026, 2, 12, 23, 59), weightGrams: 5 }, NOW),
    ).toEqual([]);
  });

  it('refuses tomorrow', () => {
    expect(
      validateHarvestInput({ harvestedAt: new Date(2026, 2, 13, 0, 0), weightGrams: 5 }, NOW),
    ).toEqual([{ field: 'harvestedAt', code: InFuture }]);
  });

  it('requires a day and a weight', () => {
    expect(validateHarvestInput({}, NOW)).toEqual([
      { field: 'harvestedAt', code: Required },
      { field: 'weightGrams', code: Required },
    ]);
  });

  it('refuses a weight that is zero, negative, not whole or not a number', () => {
    for (const weightGrams of [0, -10, 12.5, Number.NaN]) {
      expect(validateHarvestInput({ harvestedAt: NOW, weightGrams }, NOW)).toEqual([
        { field: 'weightGrams', code: NotPositiveInteger },
      ]);
    }
  });
});

describe('validateInoculationInput', () => {
  it('accepts a day months ago, with or without a quantity', () => {
    expect(validateInoculationInput({ inoculatedAt: new Date(2025, 2, 12) }, NOW)).toEqual([]);
    expect(
      validateInoculationInput({ inoculatedAt: new Date(2025, 2, 12), quantity: 40 }, NOW),
    ).toEqual([]);
  });

  it('requires a day, and refuses one after today', () => {
    expect(validateInoculationInput({}, NOW)).toEqual([{ field: 'inoculatedAt', code: Required }]);
    expect(validateInoculationInput({ inoculatedAt: new Date(2026, 2, 20) }, NOW)).toEqual([
      { field: 'inoculatedAt', code: InFuture },
    ]);
  });

  it('refuses a quantity that is not a whole number of 1 or more', () => {
    expect(validateInoculationInput({ inoculatedAt: NOW, quantity: 0 }, NOW)).toEqual([
      { field: 'quantity', code: NotPositiveInteger },
    ]);
  });
});

describe('validateJournalEntryInput', () => {
  it('requires a text for a note and an observation', () => {
    for (const kind of [JOURNAL_ENTRY_KIND.Note, JOURNAL_ENTRY_KIND.Observation]) {
      expect(validateJournalEntryInput({ kind })).toEqual([{ field: 'text', code: Required }]);
      expect(validateJournalEntryInput({ kind, text: '  ' })).toEqual([
        { field: 'text', code: Required },
      ]);
      expect(validateJournalEntryInput({ kind, text: 'Mycélium blanc' })).toEqual([]);
    }
  });

  it('accepts the other kinds without a text', () => {
    for (const kind of [
      JOURNAL_ENTRY_KIND.Soaking,
      JOURNAL_ENTRY_KIND.Watering,
      JOURNAL_ENTRY_KIND.Moving,
      JOURNAL_ENTRY_KIND.Treatment,
      JOURNAL_ENTRY_KIND.Other,
    ]) {
      expect(validateJournalEntryInput({ kind })).toEqual([]);
    }
  });
});
