import { computed, ref, type Ref } from 'vue';

import {
  INPUT_ISSUE_CODE,
  validateMushroomLogInput,
  type InputIssueCode,
} from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';
import type {
  MushroomLog,
  MushroomLogInput,
} from '@lychen/typescript-front-model-business/mushroom/MushroomLog';
import { fromDayInputValue, toDayInputValue } from '@lychen/typescript-myko/form/dayInput';
import { parseNumberInput } from '@lychen/typescript-myko/form/numberInput';

export type MushroomLogFormField = 'label' | 'zone' | 'diameterCm' | 'lengthCm';

export interface MushroomLogFormInitialValues {
  /** For a new log, the next free label the screen suggests (`B-041`). */
  label?: MushroomLog['label'];
  zoneId?: string;
  woodSpeciesId?: string;
  cutAt?: MushroomLog['cutAt'];
  diameterCm?: MushroomLog['diameterCm'];
  lengthCm?: MushroomLog['lengthCm'];
  notes?: MushroomLog['notes'];
}

export interface UseMushroomLogFormOptions {
  /** The log being edited (or what a new one starts with): the form starts on these values. */
  initialValues?: MushroomLogFormInitialValues;
}

export interface UseMushroomLogForm {
  label: Ref<string>;
  /** The id of the chosen zone, an empty string while none is chosen. */
  zoneId: Ref<string>;
  /** The id of the chosen wood species, an empty string for none. */
  woodSpeciesId: Ref<string>;
  /** The day field's value (`2026-03-12`), empty for none. */
  cutAt: Ref<string>;
  /** What is typed in the diameter field, in centimetres. */
  diameterCm: Ref<string>;
  /** What is typed in the length field, in centimetres. */
  lengthCm: Ref<string>;
  notes: Ref<string>;
  /** What is wrong with a field, once a submission has been tried (see `useHarvestForm`). */
  issue: (field: MushroomLogFormField) => InputIssueCode | undefined;
  /**
   * The input to send, or undefined (and the issues shown) when something is wrong. The label and
   * the notes are trimmed. Every field of the form is a key of the input, so that an edit knows what
   * was cleared: `woodSpecies`, `cutAt`, `diameterCm` and `lengthCm` are present and `undefined`
   * for "none", the notes an empty string (the endpoint's `mapToRequest` turns these into what the
   * API takes for "none").
   */
  submit: () => MushroomLogInput | undefined;
  /** Back to the initial values, or empty, with no issue shown. */
  reset: () => void;
}

/**
 * The state of the log form (create and edit; the batch creation of M22 has its own form): the
 * label and the zone, required, and the wood species, the cutting day, the diameter, the length
 * and the notes, optional. The label, diameter and length rules are `validateMushroomLogInput`'s,
 * the API's own; that the label is free among the person's logs and that the zone is theirs only
 * the API knows.
 */
export function useMushroomLogForm(options: UseMushroomLogFormOptions = {}): UseMushroomLogForm {
  const initial = options.initialValues;

  const label = ref(initial?.label ?? '');
  const zoneId = ref(initial?.zoneId ?? '');
  const woodSpeciesId = ref(initial?.woodSpeciesId ?? '');
  const cutAt = ref(initial?.cutAt ? toDayInputValue(initial.cutAt) : '');
  const diameterCm = ref(initial?.diameterCm === undefined ? '' : String(initial.diameterCm));
  const lengthCm = ref(initial?.lengthCm === undefined ? '' : String(initial.lengthCm));
  const notes = ref(initial?.notes ?? '');
  const hasTriedToSubmit = ref(false);

  const diameterNumber = computed(() => parseNumberInput(diameterCm.value));
  const lengthNumber = computed(() => parseNumberInput(lengthCm.value));

  const issues = computed(() => [
    ...(zoneId.value === '' ? [{ field: 'zone' as const, code: INPUT_ISSUE_CODE.Required }] : []),
    ...validateMushroomLogInput({
      label: label.value,
      diameterCm: diameterNumber.value,
      lengthCm: lengthNumber.value,
    }),
  ]);

  function issue(field: MushroomLogFormField): InputIssueCode | undefined {
    return hasTriedToSubmit.value
      ? issues.value.find((candidate) => candidate.field === field)?.code
      : undefined;
  }

  function submit(): MushroomLogInput | undefined {
    hasTriedToSubmit.value = true;

    if (issues.value.length > 0) {
      return undefined;
    }

    return {
      zone: { id: zoneId.value },
      label: label.value.trim(),
      woodSpecies: woodSpeciesId.value === '' ? undefined : { id: woodSpeciesId.value },
      cutAt: fromDayInputValue(cutAt.value),
      diameterCm: diameterNumber.value,
      lengthCm: lengthNumber.value,
      notes: notes.value.trim(),
    };
  }

  function reset(): void {
    label.value = initial?.label ?? '';
    zoneId.value = initial?.zoneId ?? '';
    woodSpeciesId.value = initial?.woodSpeciesId ?? '';
    cutAt.value = initial?.cutAt ? toDayInputValue(initial.cutAt) : '';
    diameterCm.value = initial?.diameterCm === undefined ? '' : String(initial.diameterCm);
    lengthCm.value = initial?.lengthCm === undefined ? '' : String(initial.lengthCm);
    notes.value = initial?.notes ?? '';
    hasTriedToSubmit.value = false;
  }

  return {
    label,
    zoneId,
    woodSpeciesId,
    cutAt,
    diameterCm,
    lengthCm,
    notes,
    issue,
    submit,
    reset,
  };
}
