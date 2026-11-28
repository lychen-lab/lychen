import { describe, expect, it } from 'vitest';

import { INPUT_ISSUE_CODE } from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';

import { useMushroomLogForm } from './useMushroomLogForm';

function filledForm() {
  const form = useMushroomLogForm();
  form.label.value = 'B-012';
  form.zoneId.value = 'zone-1';
  return form;
}

describe('useMushroomLogForm', () => {
  it('starts empty and says nothing yet', () => {
    const form = useMushroomLogForm();

    expect(form.label.value).toBe('');
    expect(form.zoneId.value).toBe('');
    expect(form.woodSpeciesId.value).toBe('');
    expect(form.cutAt.value).toBe('');
    expect(form.issue('label')).toBeUndefined();
    expect(form.issue('zone')).toBeUndefined();
  });

  it('starts on the values of the log being edited, or of the label suggested', () => {
    const form = useMushroomLogForm({
      initialValues: {
        label: 'B-041',
        zoneId: 'zone-1',
        woodSpeciesId: 'oak',
        cutAt: new Date(2026, 1, 3, 15, 0),
        diameterCm: 12,
        lengthCm: 40,
        notes: 'fendue',
      },
    });

    expect(form.label.value).toBe('B-041');
    expect(form.zoneId.value).toBe('zone-1');
    expect(form.woodSpeciesId.value).toBe('oak');
    expect(form.cutAt.value).toBe('2026-02-03');
    expect(form.diameterCm.value).toBe('12');
    expect(form.lengthCm.value).toBe('40');
    expect(form.notes.value).toBe('fendue');
  });

  it('asks for a zone and a label once a submission is tried', () => {
    const form = useMushroomLogForm();

    expect(form.submit()).toBeUndefined();
    expect(form.issue('zone')).toBe(INPUT_ISSUE_CODE.Required);
    expect(form.issue('label')).toBe(INPUT_ISSUE_CODE.Required);

    form.zoneId.value = 'zone-1';
    form.label.value = '   ';
    expect(form.issue('zone')).toBeUndefined();
    expect(form.issue('label')).toBe(INPUT_ISSUE_CODE.Required);
  });

  it('gives a log with a label and a zone only, every optional field present and empty', () => {
    const form = filledForm();

    expect(form.submit()).toStrictEqual({
      zone: { id: 'zone-1' },
      label: 'B-012',
      woodSpecies: undefined,
      cutAt: undefined,
      diameterCm: undefined,
      lengthCm: undefined,
      notes: '',
    });
  });

  it('gives the full log, the label and notes trimmed and the day as a local date', () => {
    const form = filledForm();
    form.label.value = '  B-013 ';
    form.woodSpeciesId.value = 'oak';
    form.cutAt.value = '2026-02-03';
    form.diameterCm.value = '12';
    form.lengthCm.value = ' 40 ';
    form.notes.value = ' fendue ';

    expect(form.submit()).toStrictEqual({
      zone: { id: 'zone-1' },
      label: 'B-013',
      woodSpecies: { id: 'oak' },
      cutAt: new Date(2026, 1, 3),
      diameterCm: 12,
      lengthCm: 40,
      notes: 'fendue',
    });
  });

  it('refuses a diameter or a length that is not a whole number of 1 or more', () => {
    const form = filledForm();

    for (const typed of ['0', '-3', '2,5', 'grosse']) {
      form.diameterCm.value = typed;
      form.lengthCm.value = typed;
      expect(form.submit()).toBeUndefined();
      expect(form.issue('diameterCm')).toBe(INPUT_ISSUE_CODE.NotPositiveInteger);
      expect(form.issue('lengthCm')).toBe(INPUT_ISSUE_CODE.NotPositiveInteger);
    }
  });

  it('lets an edit clear the wood species, the day and the sizes', () => {
    const form = useMushroomLogForm({
      initialValues: {
        label: 'B-001',
        zoneId: 'zone-1',
        woodSpeciesId: 'oak',
        cutAt: new Date(2026, 1, 3),
        diameterCm: 12,
        lengthCm: 40,
      },
    });
    form.woodSpeciesId.value = '';
    form.cutAt.value = '';
    form.diameterCm.value = '';
    form.lengthCm.value = '';

    const input = form.submit();

    expect(input).toHaveProperty('woodSpecies', undefined);
    expect(input).toHaveProperty('cutAt', undefined);
    expect(input).toHaveProperty('diameterCm', undefined);
    expect(input).toHaveProperty('lengthCm', undefined);
  });

  it('resets to the initial values and hides the issues', () => {
    const form = useMushroomLogForm({ initialValues: { label: 'B-001', zoneId: 'zone-1' } });
    form.label.value = '';
    form.submit();
    expect(form.issue('label')).toBe(INPUT_ISSUE_CODE.Required);

    form.reset();

    expect(form.label.value).toBe('B-001');
    expect(form.zoneId.value).toBe('zone-1');
    expect(form.issue('label')).toBeUndefined();
  });
});
