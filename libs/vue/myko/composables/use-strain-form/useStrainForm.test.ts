import { describe, expect, it } from 'vitest';

import { INPUT_ISSUE_CODE } from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';

import { useStrainForm } from './useStrainForm';

describe('useStrainForm', () => {
  it('starts empty and says nothing yet', () => {
    const form = useStrainForm();

    expect(form.fungusSpeciesId.value).toBe('');
    expect(form.name.value).toBe('');
    expect(form.issue('fungusSpecies')).toBeUndefined();
    expect(form.issue('name')).toBeUndefined();
  });

  it('starts on the values of the strain being edited', () => {
    const form = useStrainForm({
      initialValues: { fungusSpeciesId: 'f1', name: 'M3782', supplier: 'Mycelium+', notes: 'ok' },
    });

    expect(form.fungusSpeciesId.value).toBe('f1');
    expect(form.name.value).toBe('M3782');
    expect(form.supplier.value).toBe('Mycelium+');
    expect(form.notes.value).toBe('ok');
  });

  it('asks for a species and a name, the species first, once a submission is tried', () => {
    const form = useStrainForm();

    expect(form.submit()).toBeUndefined();
    expect(form.issue('fungusSpecies')).toBe(INPUT_ISSUE_CODE.Required);
    expect(form.issue('name')).toBe(INPUT_ISSUE_CODE.Required);

    form.fungusSpeciesId.value = 'f1';
    expect(form.issue('fungusSpecies')).toBeUndefined();
    expect(form.issue('name')).toBe(INPUT_ISSUE_CODE.Required);
  });

  it('gives the trimmed input, with the optional texts as empty strings when blank', () => {
    const form = useStrainForm();
    form.fungusSpeciesId.value = 'f1';
    form.name.value = '  M3782 ';

    expect(form.submit()).toEqual({
      fungusSpecies: { id: 'f1' },
      name: 'M3782',
      supplier: '',
      notes: '',
    });

    form.supplier.value = ' Mycelium+ ';
    form.notes.value = ' pousse vite ';
    expect(form.submit()).toMatchObject({ supplier: 'Mycelium+', notes: 'pousse vite' });
  });

  it('resets to the initial values and hides the issues', () => {
    const form = useStrainForm({ initialValues: { fungusSpeciesId: 'f1', name: 'M3782' } });
    form.name.value = '';
    form.submit();
    expect(form.issue('name')).toBe(INPUT_ISSUE_CODE.Required);

    form.reset();

    expect(form.fungusSpeciesId.value).toBe('f1');
    expect(form.name.value).toBe('M3782');
    expect(form.issue('name')).toBeUndefined();
  });
});
