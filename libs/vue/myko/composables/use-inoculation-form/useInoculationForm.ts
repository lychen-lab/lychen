import { computed, ref, toValue, type MaybeRefOrGetter, type Ref } from 'vue';

import {
  INOCULATION_METHOD,
  type InoculationInput,
  type InoculationMethod,
} from '@lychen/typescript-front-model-business/mushroom/Inoculation';
import {
  INPUT_ISSUE_CODE,
  validateInoculationInput,
  type InputIssueCode,
} from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';
import { fromDayInputValue, toDayInputValue } from '@lychen/typescript-myko/form/dayInput';
import { parseNumberInput } from '@lychen/typescript-myko/form/numberInput';

export type InoculationFormField = 'strain' | 'inoculatedAt' | 'quantity';

/** The methods in the order the chips show them: the usual one first. */
export const INOCULATION_FORM_METHODS: readonly InoculationMethod[] = [
  INOCULATION_METHOD.Dowels,
  INOCULATION_METHOD.Sawdust,
  INOCULATION_METHOD.Other,
];

/** The method a new inoculation starts on: dowels, by far the most common on logs. */
export const INOCULATION_FORM_DEFAULT_METHOD: InoculationMethod = INOCULATION_METHOD.Dowels;

export interface UseInoculationFormOptions {
  /** The log that is inoculated. */
  mushroomLogId: MaybeRefOrGetter<string>;
  /** The strain chosen when the form opens (the last one used, say). None by default. */
  initialStrainId?: string;
  /** The clock: the day the form starts on and the latest day it takes. Tests give their own. */
  now?: () => Date;
}

export interface UseInoculationForm {
  /** The id of the chosen strain, an empty string while none is chosen. */
  strainId: Ref<string>;
  method: Ref<InoculationMethod>;
  /** The day field's value (`2026-03-12`): today until changed, any earlier day is fine. */
  inoculatedAt: Ref<string>;
  /** What is typed in the quantity field (the number of dowels, say). Optional. */
  quantity: Ref<string>;
  notes: Ref<string>;
  /** The latest day the day field takes (`max`): today. */
  maxDay: Ref<string>;
  /** What is wrong with a field, once a submission has been tried (see `useHarvestForm`). */
  issue: (field: InoculationFormField) => InputIssueCode | undefined;
  /** The input to send, or undefined (and the issues shown) when something is wrong. */
  submit: () => InoculationInput | undefined;
  /** Back to the initial strain, dowels and today, as when the sheet opens again. */
  reset: () => void;
}

/**
 * The state of the inoculation form ("inoculer"): the strain, the method, the day and an optional
 * quantity. The day and quantity rules are `validateInoculationInput`'s, the API's own; that the
 * strain is the person's own only the API knows. The log must be `prepared` for the API to accept
 * the inoculation: the screen only offers the form then.
 */
export function useInoculationForm(options: UseInoculationFormOptions): UseInoculationForm {
  const now = options.now ?? (() => new Date());

  const strainId = ref(options.initialStrainId ?? '');
  const method = ref<InoculationMethod>(INOCULATION_FORM_DEFAULT_METHOD);
  const inoculatedAt = ref(toDayInputValue(now()));
  const quantity = ref('');
  const notes = ref('');
  const maxDay = ref(toDayInputValue(now()));
  const hasTriedToSubmit = ref(false);

  const inoculatedAtDate = computed(() => fromDayInputValue(inoculatedAt.value));
  const quantityNumber = computed(() => parseNumberInput(quantity.value));

  const issues = computed(() => [
    ...(strainId.value === ''
      ? [{ field: 'strain' as const, code: INPUT_ISSUE_CODE.Required }]
      : []),
    ...validateInoculationInput(
      { inoculatedAt: inoculatedAtDate.value, quantity: quantityNumber.value },
      now(),
    ),
  ]);

  function issue(field: InoculationFormField): InputIssueCode | undefined {
    return hasTriedToSubmit.value
      ? issues.value.find((candidate) => candidate.field === field)?.code
      : undefined;
  }

  function submit(): InoculationInput | undefined {
    hasTriedToSubmit.value = true;

    if (issues.value.length > 0 || !inoculatedAtDate.value) {
      return undefined;
    }

    const trimmedNotes = notes.value.trim();

    return {
      mushroomLog: { id: toValue(options.mushroomLogId) },
      strain: { id: strainId.value },
      inoculatedAt: inoculatedAtDate.value,
      method: method.value,
      ...(quantityNumber.value === undefined ? {} : { quantity: quantityNumber.value }),
      ...(trimmedNotes === '' ? {} : { notes: trimmedNotes }),
    };
  }

  function reset(): void {
    strainId.value = options.initialStrainId ?? '';
    method.value = INOCULATION_FORM_DEFAULT_METHOD;
    inoculatedAt.value = toDayInputValue(now());
    maxDay.value = toDayInputValue(now());
    quantity.value = '';
    notes.value = '';
    hasTriedToSubmit.value = false;
  }

  return { strainId, method, inoculatedAt, quantity, notes, maxDay, issue, submit, reset };
}
