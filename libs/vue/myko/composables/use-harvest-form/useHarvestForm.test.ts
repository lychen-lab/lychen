import { describe, expect, it } from 'vitest';

import { INPUT_ISSUE_CODE } from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';

import { useHarvestForm } from './useHarvestForm';

const NOW = new Date(2026, 2, 12, 22, 30);

function makeForm() {
  return useHarvestForm({ mushroomLogId: 'log-1', now: () => NOW });
}

describe('useHarvestForm', () => {
  it('starts empty, on today, and says nothing about the empty weight yet', () => {
    const form = makeForm();

    expect(form.weight.value).toBe('');
    expect(form.harvestedAt.value).toBe('2026-03-12');
    expect(form.maxDay.value).toBe('2026-03-12');
    expect(form.issue('weightGrams')).toBeUndefined();
    expect(form.issue('harvestedAt')).toBeUndefined();
  });

  it('gives the input of a valid form, on the log it was made for', () => {
    const form = makeForm();
    form.weight.value = '420';

    expect(form.submit()).toEqual({
      mushroomLog: { id: 'log-1' },
      harvestedAt: new Date(2026, 2, 12),
      weightGrams: 420,
    });
  });

  it('follows a log id that changes, given as a getter or a ref', () => {
    let id = 'log-1';
    const form = useHarvestForm({ mushroomLogId: () => id, now: () => NOW });
    form.weight.value = '10';
    id = 'log-2';

    expect(form.submit()?.mushroomLog).toEqual({ id: 'log-2' });
  });

  it('sends the notes trimmed, and leaves them out when blank', () => {
    const form = makeForm();
    form.weight.value = '10';
    form.notes.value = '  belle flush  ';
    expect(form.submit()?.notes).toBe('belle flush');

    form.notes.value = '   ';
    expect(form.submit()).not.toHaveProperty('notes');
  });

  it('refuses an empty weight and shows it only after the first try', () => {
    const form = makeForm();

    expect(form.submit()).toBeUndefined();
    expect(form.issue('weightGrams')).toBe(INPUT_ISSUE_CODE.Required);
    expect(form.issue('harvestedAt')).toBeUndefined();
  });

  it('refuses a weight that is not a whole number of grams, above 0', () => {
    const form = makeForm();

    for (const typed of ['0', '-5', '12,5', 'abc']) {
      form.weight.value = typed;
      expect(form.submit()).toBeUndefined();
      expect(form.issue('weightGrams')).toBe(INPUT_ISSUE_CODE.NotPositiveInteger);
    }
  });

  it('refuses a day in the future and a day that is missing', () => {
    const form = makeForm();
    form.weight.value = '10';

    form.harvestedAt.value = '2026-03-13';
    expect(form.submit()).toBeUndefined();
    expect(form.issue('harvestedAt')).toBe(INPUT_ISSUE_CODE.InFuture);

    form.harvestedAt.value = '';
    expect(form.submit()).toBeUndefined();
    expect(form.issue('harvestedAt')).toBe(INPUT_ISSUE_CODE.Required);
  });

  it('accepts today and a day in the past, even late in the evening', () => {
    const form = makeForm();
    form.weight.value = '10';

    form.harvestedAt.value = '2026-03-12';
    expect(form.submit()).toBeDefined();

    form.harvestedAt.value = '2025-10-01';
    expect(form.submit()?.harvestedAt).toEqual(new Date(2025, 9, 1));
  });

  it('follows what is typed once a submission has been tried', () => {
    const form = makeForm();
    form.submit();
    expect(form.issue('weightGrams')).toBe(INPUT_ISSUE_CODE.Required);

    form.weight.value = '10';
    expect(form.issue('weightGrams')).toBeUndefined();
  });

  it('resets to an empty weight, on the day of the new now, with no issue shown', () => {
    let current = NOW;
    const form = useHarvestForm({ mushroomLogId: 'log-1', now: () => current });
    form.weight.value = '99';
    form.notes.value = 'x';
    form.harvestedAt.value = '2026-01-01';
    form.submit();

    current = new Date(2026, 2, 13, 8, 0);
    form.reset();

    expect(form.weight.value).toBe('');
    expect(form.notes.value).toBe('');
    expect(form.harvestedAt.value).toBe('2026-03-13');
    expect(form.maxDay.value).toBe('2026-03-13');
    expect(form.issue('weightGrams')).toBeUndefined();
  });
});
