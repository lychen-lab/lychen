import { describe, expect, it } from 'vitest';

import { INOCULATION_METHOD } from '@lychen/typescript-front-model-business/mushroom/Inoculation';
import { INPUT_ISSUE_CODE } from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';

import {
  INOCULATION_FORM_DEFAULT_METHOD,
  INOCULATION_FORM_METHODS,
  useInoculationForm,
} from './useInoculationForm';

const NOW = new Date(2026, 2, 12, 22, 30);

function makeForm(initialStrainId?: string) {
  return useInoculationForm({ mushroomLogId: 'log-1', initialStrainId, now: () => NOW });
}

describe('INOCULATION_FORM_METHODS', () => {
  it('offers every method once, the default first', () => {
    expect([...INOCULATION_FORM_METHODS].sort()).toEqual(Object.values(INOCULATION_METHOD).sort());
    expect(INOCULATION_FORM_METHODS[0]).toBe(INOCULATION_FORM_DEFAULT_METHOD);
  });
});

describe('useInoculationForm', () => {
  it('starts on dowels and today, with no strain, and shows no issue yet', () => {
    const form = makeForm();

    expect(form.strainId.value).toBe('');
    expect(form.method.value).toBe(INOCULATION_METHOD.Dowels);
    expect(form.inoculatedAt.value).toBe('2026-03-12');
    expect(form.maxDay.value).toBe('2026-03-12');
    expect(form.quantity.value).toBe('');
    expect(form.issue('strain')).toBeUndefined();
  });

  it('asks for a strain', () => {
    const form = makeForm();

    expect(form.submit()).toBeUndefined();
    expect(form.issue('strain')).toBe(INPUT_ISSUE_CODE.Required);
    expect(form.issue('inoculatedAt')).toBeUndefined();
    expect(form.issue('quantity')).toBeUndefined();
  });

  it('gives the input of a valid form: a strain is all it takes', () => {
    const form = makeForm();
    form.strainId.value = 'strain-1';

    expect(form.submit()).toEqual({
      mushroomLog: { id: 'log-1' },
      strain: { id: 'strain-1' },
      inoculatedAt: new Date(2026, 2, 12),
      method: INOCULATION_METHOD.Dowels,
    });
  });

  it('starts on the initial strain, and goes back to it on reset', () => {
    const form = makeForm('strain-9');
    expect(form.strainId.value).toBe('strain-9');

    form.strainId.value = 'strain-1';
    form.reset();
    expect(form.strainId.value).toBe('strain-9');
  });

  it('accepts a day months ago, since logs inoculated before the app are entered too', () => {
    const form = makeForm('strain-1');
    form.inoculatedAt.value = '2025-03-12';

    expect(form.submit()?.inoculatedAt).toEqual(new Date(2025, 2, 12));
  });

  it('refuses a day in the future and a missing day', () => {
    const form = makeForm('strain-1');

    form.inoculatedAt.value = '2026-03-13';
    expect(form.submit()).toBeUndefined();
    expect(form.issue('inoculatedAt')).toBe(INPUT_ISSUE_CODE.InFuture);

    form.inoculatedAt.value = '';
    expect(form.submit()).toBeUndefined();
    expect(form.issue('inoculatedAt')).toBe(INPUT_ISSUE_CODE.Required);
  });

  it('leaves the quantity out when blank, and sends it when given', () => {
    const form = makeForm('strain-1');
    expect(form.submit()).not.toHaveProperty('quantity');

    form.quantity.value = '40';
    expect(form.submit()?.quantity).toBe(40);
  });

  it('refuses a quantity that is not a whole number of 1 or more', () => {
    const form = makeForm('strain-1');

    for (const typed of ['0', '-3', '2,5', 'beaucoup']) {
      form.quantity.value = typed;
      expect(form.submit()).toBeUndefined();
      expect(form.issue('quantity')).toBe(INPUT_ISSUE_CODE.NotPositiveInteger);
    }
  });

  it('sends the chosen method', () => {
    const form = makeForm('strain-1');
    form.method.value = INOCULATION_METHOD.Sawdust;

    expect(form.submit()?.method).toBe(INOCULATION_METHOD.Sawdust);
  });

  it('sends the notes trimmed, and leaves them out when blank', () => {
    const form = makeForm('strain-1');
    form.notes.value = '  bois très humide ';
    expect(form.submit()?.notes).toBe('bois très humide');

    form.notes.value = '  ';
    expect(form.submit()).not.toHaveProperty('notes');
  });

  it('follows a log id that changes', () => {
    let id = 'log-1';
    const form = useInoculationForm({
      mushroomLogId: () => id,
      initialStrainId: 's',
      now: () => NOW,
    });
    id = 'log-2';

    expect(form.submit()?.mushroomLog).toEqual({ id: 'log-2' });
  });

  it('follows what is typed once a submission has been tried', () => {
    const form = makeForm();
    form.submit();
    expect(form.issue('strain')).toBe(INPUT_ISSUE_CODE.Required);

    form.strainId.value = 'strain-1';
    expect(form.issue('strain')).toBeUndefined();
  });

  it('resets every field and hides the issues', () => {
    const form = makeForm();
    form.strainId.value = 'strain-1';
    form.method.value = INOCULATION_METHOD.Other;
    form.inoculatedAt.value = '2025-01-01';
    form.quantity.value = '12';
    form.notes.value = 'x';
    form.quantity.value = 'abc';
    form.submit();

    form.reset();

    expect(form.strainId.value).toBe('');
    expect(form.method.value).toBe(INOCULATION_METHOD.Dowels);
    expect(form.inoculatedAt.value).toBe('2026-03-12');
    expect(form.quantity.value).toBe('');
    expect(form.notes.value).toBe('');
    expect(form.issue('quantity')).toBeUndefined();
  });
});
