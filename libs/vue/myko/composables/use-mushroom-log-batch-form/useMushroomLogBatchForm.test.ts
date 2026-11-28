import { describe, expect, it } from 'vitest';

import { INOCULATION_METHOD } from '@lychen/typescript-front-model-business/mushroom/Inoculation';
import { INPUT_ISSUE_CODE } from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';

import { useMushroomLogBatchForm } from './useMushroomLogBatchForm';

const NOW = new Date(2026, 2, 12, 22, 30);

function filledForm() {
  const form = useMushroomLogBatchForm({ now: () => NOW });
  form.prefix.value = 'B-';
  form.count.value = '40';
  form.zoneId.value = 'zone-1';
  return form;
}

describe('useMushroomLogBatchForm', () => {
  it('starts at 1 with three digits, no count and no zone, and says nothing yet', () => {
    const form = useMushroomLogBatchForm({ now: () => NOW });

    expect(form.prefix.value).toBe('');
    expect(form.startNumber.value).toBe('1');
    expect(form.count.value).toBe('');
    expect(form.digits.value).toBe('3');
    expect(form.zoneId.value).toBe('');
    expect(form.woodSpeciesId.value).toBe('');
    expect(form.inoculate.value).toBe(false);
    expect(form.labels.value).toEqual([]);
    expect(form.issue('count')).toBeUndefined();
    expect(form.issue('zone')).toBeUndefined();
  });

  it('starts on the values the screen suggests', () => {
    const form = useMushroomLogBatchForm({
      initialValues: {
        prefix: 'B-',
        startNumber: 41,
        digits: 4,
        zoneId: 'zone-1',
        woodSpeciesId: 'oak',
      },
      initialStrainId: 'strain-9',
      now: () => NOW,
    });

    expect(form.prefix.value).toBe('B-');
    expect(form.startNumber.value).toBe('41');
    expect(form.digits.value).toBe('4');
    expect(form.zoneId.value).toBe('zone-1');
    expect(form.woodSpeciesId.value).toBe('oak');
    expect(form.inoculation.strainId.value).toBe('strain-9');
  });

  it('previews the labels as soon as the numbering is valid', () => {
    const form = filledForm();
    form.startNumber.value = '1';
    form.count.value = '3';

    expect(form.labels.value).toEqual(['B-001', 'B-002', 'B-003']);

    form.digits.value = '2';
    form.prefix.value = 'S';
    expect(form.labels.value).toEqual(['S01', 'S02', 'S03']);

    form.count.value = '201';
    expect(form.labels.value).toEqual([]);
  });

  it('asks for a zone and a count once a submission is tried', () => {
    const form = useMushroomLogBatchForm({ now: () => NOW });

    expect(form.submit()).toBeUndefined();
    expect(form.issue('zone')).toBe(INPUT_ISSUE_CODE.Required);
    expect(form.issue('count')).toBe(INPUT_ISSUE_CODE.Required);
    expect(form.issue('startNumber')).toBeUndefined();
    expect(form.issue('digits')).toBeUndefined();
  });

  it('refuses a count out of 1 to 200, a fraction or a word (a wrong value, not a missing one)', () => {
    const form = filledForm();

    for (const typed of ['0', '201', '-3', '2,5', 'beaucoup']) {
      form.count.value = typed;
      expect(form.submit()).toBeUndefined();
      expect(form.issue('count')).toBe(INPUT_ISSUE_CODE.OutOfRange);
    }
  });

  it('refuses digits out of 1 to 6 and a negative first number', () => {
    const form = filledForm();
    form.digits.value = '7';
    form.startNumber.value = '-1';

    expect(form.submit()).toBeUndefined();
    expect(form.issue('digits')).toBe(INPUT_ISSUE_CODE.OutOfRange);
    expect(form.issue('startNumber')).toBe(INPUT_ISSUE_CODE.OutOfRange);
  });

  it('gives the numbering and the zone, and nothing else, for a plain batch', () => {
    const form = filledForm();

    expect(form.submit()).toStrictEqual({
      prefix: 'B-',
      startNumber: 1,
      count: 40,
      digits: 3,
      zone: { id: 'zone-1' },
    });
  });

  it('keeps the prefix as typed, spaces included, and takes an empty one', () => {
    const form = filledForm();
    form.prefix.value = '';

    expect(form.submit()).toMatchObject({ prefix: '' });
  });

  it('gives the wood species when one is chosen', () => {
    const form = filledForm();
    form.woodSpeciesId.value = 'oak';

    expect(form.submit()).toMatchObject({ woodSpecies: { id: 'oak' } });
  });

  it('leaves the inoculation out while it is off, whatever was typed in it', () => {
    const form = filledForm();
    form.inoculation.strainId.value = 'strain-1';

    expect(form.submit()).not.toHaveProperty('inoculation');
  });

  it('gives the inoculation of the whole batch, without a log, when it is on', () => {
    const form = filledForm();
    form.inoculate.value = true;
    form.inoculation.strainId.value = 'strain-1';
    form.inoculation.quantity.value = '12';

    expect(form.submit()).toStrictEqual({
      prefix: 'B-',
      startNumber: 1,
      count: 40,
      digits: 3,
      zone: { id: 'zone-1' },
      inoculation: {
        strain: { id: 'strain-1' },
        inoculatedAt: new Date(2026, 2, 12),
        method: INOCULATION_METHOD.Dowels,
        quantity: 12,
      },
    });
  });

  it('asks for a strain, and shows it with the numbering issues, when the inoculation is on', () => {
    const form = useMushroomLogBatchForm({ now: () => NOW });
    form.inoculate.value = true;

    expect(form.submit()).toBeUndefined();
    expect(form.issue('count')).toBe(INPUT_ISSUE_CODE.Required);
    expect(form.inoculationIssue('strain')).toBe(INPUT_ISSUE_CODE.Required);
  });

  it('shows no inoculation issue while the inoculation is off', () => {
    const form = useMushroomLogBatchForm({ now: () => NOW });

    expect(form.submit()).toBeUndefined();
    expect(form.inoculationIssue('strain')).toBeUndefined();
  });

  it('refuses an inoculation day in the future', () => {
    const form = filledForm();
    form.inoculate.value = true;
    form.inoculation.strainId.value = 'strain-1';
    form.inoculation.inoculatedAt.value = '2026-03-13';

    expect(form.submit()).toBeUndefined();
    expect(form.inoculationIssue('inoculatedAt')).toBe(INPUT_ISSUE_CODE.InFuture);
  });

  it('resets to the initial values and hides the issues', () => {
    const form = useMushroomLogBatchForm({
      initialValues: { prefix: 'B-', startNumber: 41, zoneId: 'zone-1' },
      now: () => NOW,
    });
    form.count.value = '0';
    form.inoculate.value = true;
    form.submit();
    expect(form.issue('count')).toBe(INPUT_ISSUE_CODE.OutOfRange);

    form.reset();

    expect(form.count.value).toBe('');
    expect(form.inoculate.value).toBe(false);
    expect(form.zoneId.value).toBe('zone-1');
    expect(form.startNumber.value).toBe('41');
    expect(form.issue('count')).toBeUndefined();
    expect(form.inoculationIssue('strain')).toBeUndefined();
  });
});
