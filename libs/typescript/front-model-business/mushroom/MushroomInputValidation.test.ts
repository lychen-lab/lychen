import { describe, expect, it } from 'vitest';

import { JOURNAL_ENTRY_KIND } from './JournalEntry';
import {
  MUSHROOM_LOG_BATCH_MAX_COUNT,
  MUSHROOM_LOG_BATCH_MAX_DIGITS,
  isValidMushroomLogBatchNumbering,
} from './MushroomLogBatchLabels';
import {
  INPUT_ISSUE_CODE,
  ZONE_NAME_MAX_LENGTH,
  validateHarvestInput,
  validateInoculationInput,
  validateJournalEntryInput,
  validateMushroomLogBatchInput,
  validateMushroomLogInput,
  validateStrainInput,
  validateZoneInput,
} from './MushroomInputValidation';

const { Required, TooLong, NotPositiveInteger, InFuture, OutOfRange } = INPUT_ISSUE_CODE;

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

describe('validateStrainInput', () => {
  it('accepts a species and a name', () => {
    expect(validateStrainInput({ fungusSpecies: { id: 'f1' }, name: 'M3782' })).toEqual([]);
  });

  it('requires a species, and an id left blank is none', () => {
    expect(validateStrainInput({ name: 'M3782' })).toEqual([
      { field: 'fungusSpecies', code: Required },
    ]);
    expect(validateStrainInput({ fungusSpecies: { id: '' }, name: 'M3782' })).toEqual([
      { field: 'fungusSpecies', code: Required },
    ]);
  });

  it('requires a name, and a blank one is none', () => {
    expect(validateStrainInput({ fungusSpecies: { id: 'f1' } })).toEqual([
      { field: 'name', code: Required },
    ]);
    expect(validateStrainInput({ fungusSpecies: { id: 'f1' }, name: '  ' })).toEqual([
      { field: 'name', code: Required },
    ]);
  });

  it('reports both, the species first', () => {
    expect(validateStrainInput({})).toEqual([
      { field: 'fungusSpecies', code: Required },
      { field: 'name', code: Required },
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

describe('validateMushroomLogBatchInput', () => {
  it('accepts a numbering', () => {
    expect(validateMushroomLogBatchInput({ startNumber: 1, count: 40, digits: 3 })).toEqual([]);
  });

  it('requires the first number, the count and the digits', () => {
    expect(validateMushroomLogBatchInput({})).toEqual([
      { field: 'startNumber', code: Required },
      { field: 'count', code: Required },
      { field: 'digits', code: Required },
    ]);
  });

  it('takes a first number from 0, but not below or a fraction', () => {
    expect(validateMushroomLogBatchInput({ startNumber: 0, count: 1, digits: 1 })).toEqual([]);
    expect(validateMushroomLogBatchInput({ startNumber: -1, count: 1, digits: 1 })).toEqual([
      { field: 'startNumber', code: OutOfRange },
    ]);
    expect(validateMushroomLogBatchInput({ startNumber: 1.5, count: 1, digits: 1 })).toEqual([
      { field: 'startNumber', code: OutOfRange },
    ]);
  });

  it('takes from 1 to the most logs a batch creates', () => {
    function issues(count: number) {
      return validateMushroomLogBatchInput({ startNumber: 1, count, digits: 3 });
    }

    expect(issues(1)).toEqual([]);
    expect(issues(MUSHROOM_LOG_BATCH_MAX_COUNT)).toEqual([]);
    expect(issues(0)).toEqual([{ field: 'count', code: OutOfRange }]);
    expect(issues(MUSHROOM_LOG_BATCH_MAX_COUNT + 1)).toEqual([
      { field: 'count', code: OutOfRange },
    ]);
    expect(issues(2.5)).toEqual([{ field: 'count', code: OutOfRange }]);
  });

  it('takes from 1 to the most digits a number is padded to', () => {
    function issues(digits: number) {
      return validateMushroomLogBatchInput({ startNumber: 1, count: 3, digits });
    }

    expect(issues(1)).toEqual([]);
    expect(issues(MUSHROOM_LOG_BATCH_MAX_DIGITS)).toEqual([]);
    expect(issues(0)).toEqual([{ field: 'digits', code: OutOfRange }]);
    expect(issues(MUSHROOM_LOG_BATCH_MAX_DIGITS + 1)).toEqual([
      { field: 'digits', code: OutOfRange },
    ]);
  });

  it('agrees with the numbering that makes labels', () => {
    for (const numbering of [
      { prefix: 'B-', startNumber: 1, count: 40, digits: 3 },
      { prefix: 'B-', startNumber: 1, count: 0, digits: 3 },
      { prefix: 'B-', startNumber: 1, count: 201, digits: 3 },
      { prefix: 'B-', startNumber: 1, count: 5, digits: 7 },
    ]) {
      expect(validateMushroomLogBatchInput(numbering)).toEqual(
        isValidMushroomLogBatchNumbering(numbering)
          ? []
          : expect.arrayContaining([expect.objectContaining({ code: OutOfRange })]),
      );
    }
  });
});
