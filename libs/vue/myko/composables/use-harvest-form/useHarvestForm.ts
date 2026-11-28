import { computed, ref, toValue, type MaybeRefOrGetter, type Ref } from 'vue';

import type { HarvestInput } from '@lychen/typescript-front-model-business/mushroom/Harvest';
import {
  validateHarvestInput,
  type InputIssueCode,
} from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';
import { fromDayInputValue, toDayInputValue } from '@lychen/typescript-myko/form/dayInput';

export type HarvestFormField = 'harvestedAt' | 'weightGrams';

export interface UseHarvestFormOptions {
  /** The log the harvest is weighed on. */
  mushroomLogId: MaybeRefOrGetter<string>;
  /** The clock: the day the form starts on and the latest day it takes. Tests give their own. */
  now?: () => Date;
}

export interface UseHarvestForm {
  /** What is typed in the weight field, in grams. */
  weight: Ref<string>;
  /** The day field's value (`2026-03-12`): today until changed. */
  harvestedAt: Ref<string>;
  notes: Ref<string>;
  /** The latest day the day field takes (`max`): today. */
  maxDay: Ref<string>;
  /**
   * What is wrong with a field, once a submission has been tried: a form that scolds an empty field
   * before it is touched is noise. After that first try it follows what is typed.
   */
  issue: (field: HarvestFormField) => InputIssueCode | undefined;
  /** The input to send, or undefined (and the issues shown) when something is wrong. */
  submit: () => HarvestInput | undefined;
  /** Back to an empty weight and today, as when the sheet opens again. */
  reset: () => void;
}

/**
 * Reads a whole number of grams from what is typed. Undefined for a blank field; NaN for text
 * that is not a number (`12 g`, `1e3`), so that the validation refuses it as a weight rather than
 * as a missing one. A decimal comma is read as a point: phones in French type `12,5`.
 */
export function parseWeightGrams(text: string): number | undefined {
  const trimmed = text.trim();

  if (trimmed === '') {
    return undefined;
  }

  return /^[+-]?\d+([.,]\d+)?$/.test(trimmed) ? Number(trimmed.replace(',', '.')) : Number.NaN;
}

/**
 * The state of the harvest form ("peser une récolte"): what the fields hold, the issues to show
 * and the `HarvestInput` it gives. The rules are `validateHarvestInput`'s, the API's own.
 */
export function useHarvestForm(options: UseHarvestFormOptions): UseHarvestForm {
  const now = options.now ?? (() => new Date());

  const weight = ref('');
  const harvestedAt = ref(toDayInputValue(now()));
  const notes = ref('');
  const maxDay = ref(toDayInputValue(now()));
  const hasTriedToSubmit = ref(false);

  const harvestedAtDate = computed(() => fromDayInputValue(harvestedAt.value));
  const weightGrams = computed(() => parseWeightGrams(weight.value));

  const issues = computed(() =>
    validateHarvestInput(
      { harvestedAt: harvestedAtDate.value, weightGrams: weightGrams.value },
      now(),
    ),
  );

  function issue(field: HarvestFormField): InputIssueCode | undefined {
    return hasTriedToSubmit.value
      ? issues.value.find((candidate) => candidate.field === field)?.code
      : undefined;
  }

  function submit(): HarvestInput | undefined {
    hasTriedToSubmit.value = true;

    if (issues.value.length > 0 || !harvestedAtDate.value || weightGrams.value === undefined) {
      return undefined;
    }

    const trimmedNotes = notes.value.trim();

    return {
      mushroomLog: { id: toValue(options.mushroomLogId) },
      harvestedAt: harvestedAtDate.value,
      weightGrams: weightGrams.value,
      ...(trimmedNotes === '' ? {} : { notes: trimmedNotes }),
    };
  }

  function reset(): void {
    weight.value = '';
    notes.value = '';
    harvestedAt.value = toDayInputValue(now());
    maxDay.value = toDayInputValue(now());
    hasTriedToSubmit.value = false;
  }

  return { weight, harvestedAt, notes, maxDay, issue, submit, reset };
}
