import { describe, expect, it } from 'vitest';

import {
  INPUT_ISSUE_CODE,
  ZONE_NAME_MAX_LENGTH,
} from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';

import { useZoneForm } from './useZoneForm';

describe('useZoneForm', () => {
  it('starts empty and says nothing about the empty name yet', () => {
    const form = useZoneForm();

    expect(form.name.value).toBe('');
    expect(form.description.value).toBe('');
    expect(form.issue('name')).toBeUndefined();
  });

  it('starts on the values of the zone being edited', () => {
    const form = useZoneForm({ initialValues: { name: 'Sous-bois', description: 'Au nord' } });

    expect(form.name.value).toBe('Sous-bois');
    expect(form.description.value).toBe('Au nord');
  });

  it('gives the trimmed name and description', () => {
    const form = useZoneForm();
    form.name.value = '  Sous-bois ';
    form.description.value = ' Ombragé, humide ';

    expect(form.submit()).toEqual({ name: 'Sous-bois', description: 'Ombragé, humide' });
  });

  it('gives an empty description rather than none, so that an edit can clear it', () => {
    const form = useZoneForm({ initialValues: { name: 'Cave', description: 'Fraîche' } });
    form.description.value = '   ';

    expect(form.submit()).toEqual({ name: 'Cave', description: '' });
  });

  it('refuses a missing or blank name, and shows it only after the first try', () => {
    const form = useZoneForm();
    form.name.value = '   ';
    expect(form.issue('name')).toBeUndefined();

    expect(form.submit()).toBeUndefined();
    expect(form.issue('name')).toBe(INPUT_ISSUE_CODE.Required);
  });

  it('refuses a name longer than the API takes, and accepts the longest one', () => {
    const form = useZoneForm();

    form.name.value = 'a'.repeat(ZONE_NAME_MAX_LENGTH + 1);
    expect(form.submit()).toBeUndefined();
    expect(form.issue('name')).toBe(INPUT_ISSUE_CODE.TooLong);

    form.name.value = 'a'.repeat(ZONE_NAME_MAX_LENGTH);
    expect(form.submit()).toBeDefined();
    expect(form.issue('name')).toBeUndefined();
  });

  it('resets to the initial values and hides the issues', () => {
    const form = useZoneForm({ initialValues: { name: 'Cave' } });
    form.name.value = '';
    form.submit();
    expect(form.issue('name')).toBe(INPUT_ISSUE_CODE.Required);

    form.reset();

    expect(form.name.value).toBe('Cave');
    expect(form.description.value).toBe('');
    expect(form.issue('name')).toBeUndefined();
  });
});
