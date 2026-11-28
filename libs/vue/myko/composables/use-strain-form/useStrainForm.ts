import { computed, ref, type Ref } from 'vue';

import {
  validateStrainInput,
  type InputIssueCode,
} from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';
import type { Strain, StrainInput } from '@lychen/typescript-front-model-business/mushroom/Strain';

export type StrainFormField = 'fungusSpecies' | 'name';

export interface UseStrainFormOptions {
  /** The strain being edited, when it is one: the form starts on its values. */
  initialValues?: Pick<Strain, 'name' | 'supplier' | 'notes'> & { fungusSpeciesId?: string };
}

export interface UseStrainForm {
  /** The id of the chosen species, an empty string while none is chosen. */
  fungusSpeciesId: Ref<string>;
  name: Ref<string>;
  supplier: Ref<string>;
  notes: Ref<string>;
  /** What is wrong with a field, once a submission has been tried (see `useHarvestForm`). */
  issue: (field: StrainFormField) => InputIssueCode | undefined;
  /**
   * The input to send, or undefined (and the issues shown) when something is wrong. The texts are
   * trimmed; a supplier or notes left blank are empty strings, not left out, so that an edit can
   * clear them (see `useZoneForm`).
   */
  submit: () => StrainInput | undefined;
  /** Back to the initial values, or empty, with no issue shown. */
  reset: () => void;
}

/**
 * The state of the strain form (create and edit): the species (from the reference data) and the
 * name, both required, and an optional supplier and notes. The rules are `validateStrainInput`'s,
 * the API's own.
 */
export function useStrainForm(options: UseStrainFormOptions = {}): UseStrainForm {
  const initial = options.initialValues;

  const fungusSpeciesId = ref(initial?.fungusSpeciesId ?? '');
  const name = ref(initial?.name ?? '');
  const supplier = ref(initial?.supplier ?? '');
  const notes = ref(initial?.notes ?? '');
  const hasTriedToSubmit = ref(false);

  const issues = computed(() =>
    validateStrainInput({ fungusSpecies: { id: fungusSpeciesId.value }, name: name.value }),
  );

  function issue(field: StrainFormField): InputIssueCode | undefined {
    return hasTriedToSubmit.value
      ? issues.value.find((candidate) => candidate.field === field)?.code
      : undefined;
  }

  function submit(): StrainInput | undefined {
    hasTriedToSubmit.value = true;

    return issues.value.length > 0
      ? undefined
      : {
          fungusSpecies: { id: fungusSpeciesId.value },
          name: name.value.trim(),
          supplier: supplier.value.trim(),
          notes: notes.value.trim(),
        };
  }

  function reset(): void {
    fungusSpeciesId.value = initial?.fungusSpeciesId ?? '';
    name.value = initial?.name ?? '';
    supplier.value = initial?.supplier ?? '';
    notes.value = initial?.notes ?? '';
    hasTriedToSubmit.value = false;
  }

  return { fungusSpeciesId, name, supplier, notes, issue, submit, reset };
}
