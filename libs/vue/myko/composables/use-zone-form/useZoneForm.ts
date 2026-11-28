import { computed, ref, type Ref } from 'vue';

import {
  validateZoneInput,
  type InputIssueCode,
} from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';
import type { Zone, ZoneInput } from '@lychen/typescript-front-model-business/mushroom/Zone';

export type ZoneFormField = 'name';

export interface UseZoneFormOptions {
  /** The zone being edited, when it is one: the form starts on its values. */
  initialValues?: Pick<Zone, 'name' | 'description'>;
}

export interface UseZoneForm {
  name: Ref<string>;
  description: Ref<string>;
  /** What is wrong with a field, once a submission has been tried (see `useHarvestForm`). */
  issue: (field: ZoneFormField) => InputIssueCode | undefined;
  /**
   * The input to send, or undefined (and the issues shown) when something is wrong. The name and
   * the description are trimmed; a description left blank is an empty string, not left out, so that
   * an edit can clear it (the endpoint's mapper turns it into what the API takes for "none").
   */
  submit: () => ZoneInput | undefined;
  /** Back to the initial values, or empty, with no issue shown. */
  reset: () => void;
}

/**
 * The state of the zone form (create and edit): a name, required and at most
 * `ZONE_NAME_MAX_LENGTH` characters, and an optional description. The rules are
 * `validateZoneInput`'s, the API's own. Deleting a zone that holds logs is a matter of the screen
 * and the API, not of this form.
 */
export function useZoneForm(options: UseZoneFormOptions = {}): UseZoneForm {
  const name = ref(options.initialValues?.name ?? '');
  const description = ref(options.initialValues?.description ?? '');
  const hasTriedToSubmit = ref(false);

  const issues = computed(() => validateZoneInput({ name: name.value }));

  function issue(field: ZoneFormField): InputIssueCode | undefined {
    return hasTriedToSubmit.value
      ? issues.value.find((candidate) => candidate.field === field)?.code
      : undefined;
  }

  function submit(): ZoneInput | undefined {
    hasTriedToSubmit.value = true;

    return issues.value.length > 0
      ? undefined
      : { name: name.value.trim(), description: description.value.trim() };
  }

  function reset(): void {
    name.value = options.initialValues?.name ?? '';
    description.value = options.initialValues?.description ?? '';
    hasTriedToSubmit.value = false;
  }

  return { name, description, issue, submit, reset };
}
