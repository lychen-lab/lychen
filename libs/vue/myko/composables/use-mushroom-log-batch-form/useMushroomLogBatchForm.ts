import { computed, ref, type Ref } from 'vue';

import {
  INPUT_ISSUE_CODE,
  validateMushroomLogBatchInput,
  type InputIssueCode,
} from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';
import type { MushroomLogBatchInput } from '@lychen/typescript-front-model-business/mushroom/MushroomLogBatchInput';
import { mushroomLogBatchLabels } from '@lychen/typescript-front-model-business/mushroom/MushroomLogBatchLabels';
import type {
  InoculationInput,
  InoculationMethod,
} from '@lychen/typescript-front-model-business/mushroom/Inoculation';
import { parseNumberInput } from '@lychen/typescript-myko/form/numberInput';

import {
  useInoculationForm,
  type InoculationFormField,
} from '../use-inoculation-form/useInoculationForm';

export type MushroomLogBatchFormField = 'prefix' | 'startNumber' | 'count' | 'digits' | 'zone';

/** The digits a batch's numbers are padded to unless changed: `B-001`, enough for a thousand logs. */
export const MUSHROOM_LOG_BATCH_FORM_DEFAULT_DIGITS = 3;

/** The first number of a batch unless the screen suggests another (the one after the last used). */
export const MUSHROOM_LOG_BATCH_FORM_DEFAULT_START = 1;

export interface MushroomLogBatchFormInitialValues {
  /** The prefix the person's labels already share (`B-`), when the screen found one. */
  prefix?: string;
  /** The first number free after the person's last log with that prefix. */
  startNumber?: number;
  digits?: number;
  zoneId?: string;
  woodSpeciesId?: string;
}

export interface UseMushroomLogBatchFormOptions {
  initialValues?: MushroomLogBatchFormInitialValues;
  /** The strain the inoculation starts on (the last one used, say). None by default. */
  initialStrainId?: string;
  /** The clock of the inoculation's day. Tests give their own. */
  now?: () => Date;
}

export interface UseMushroomLogBatchForm {
  /** What comes before the number in every label (`B-`). Free, and may be empty. */
  prefix: Ref<string>;
  /** What is typed in the first number's field. */
  startNumber: Ref<string>;
  /** What is typed in the count's field: how many logs. Empty until the person types one. */
  count: Ref<string>;
  /** What is typed in the digits' field: how many digits each number is padded to. */
  digits: Ref<string>;
  /** The id of the chosen zone, an empty string while none is chosen. */
  zoneId: Ref<string>;
  /** The id of the chosen wood species, an empty string for none. */
  woodSpeciesId: Ref<string>;
  /** Whether the whole batch is inoculated at once (off by default: most batches are not yet). */
  inoculate: Ref<boolean>;
  /** The inoculation's strain, method, day and quantity, used while `inoculate` is on. */
  inoculation: {
    strainId: Ref<string>;
    method: Ref<InoculationMethod>;
    inoculatedAt: Ref<string>;
    quantity: Ref<string>;
    maxDay: Ref<string>;
  };
  /**
   * The labels the batch creates, in order, for the preview; empty while the numbering is not
   * valid (no preview until the person has typed a count).
   */
  labels: Ref<string[]>;
  /** What is wrong with a field, once a submission has been tried (see `useHarvestForm`). */
  issue: (field: MushroomLogBatchFormField) => InputIssueCode | undefined;
  /** What is wrong with a field of the inoculation, once a submission has been tried. */
  inoculationIssue: (field: InoculationFormField) => InputIssueCode | undefined;
  /** The input to send, or undefined (and the issues shown) when something is wrong. */
  submit: () => MushroomLogBatchInput | undefined;
  /** Back to the initial values, or empty, with no issue shown. */
  reset: () => void;
}

/**
 * What every log of the batch gets: the form's inoculation without its log, which does not exist
 * yet (the screen links each one as it creates them).
 */
function withoutLog(input: InoculationInput): NonNullable<MushroomLogBatchInput['inoculation']> {
  return {
    strain: input.strain,
    inoculatedAt: input.inoculatedAt,
    method: input.method,
    ...(input.quantity === undefined ? {} : { quantity: input.quantity }),
    ...(input.notes === undefined ? {} : { notes: input.notes }),
  };
}

/**
 * The state of the batch form ("créer B-001 à B-040 dans la zone Sous-bois"): the prefix, the first
 * number, the count and the digits that make the labels, the zone (required), a wood species and an
 * inoculation for the whole batch (both optional). The numbering rules are
 * `validateMushroomLogBatchInput`'s, and `labels` is what the creation will make, so that the
 * preview and the creation cannot differ. That the labels are free among the person's logs and that
 * the zone is theirs only the API knows: the screen shows its answer.
 *
 * The inoculation is the one of `useInoculationForm` (its rules, its defaults: dowels, today), kept
 * for the whole batch only while `inoculate` is on.
 */
export function useMushroomLogBatchForm(
  options: UseMushroomLogBatchFormOptions = {},
): UseMushroomLogBatchForm {
  const initial = options.initialValues;

  function initialStart(): string {
    return String(initial?.startNumber ?? MUSHROOM_LOG_BATCH_FORM_DEFAULT_START);
  }

  function initialDigits(): string {
    return String(initial?.digits ?? MUSHROOM_LOG_BATCH_FORM_DEFAULT_DIGITS);
  }

  const prefix = ref(initial?.prefix ?? '');
  const startNumber = ref(initialStart());
  const count = ref('');
  const digits = ref(initialDigits());
  const zoneId = ref(initial?.zoneId ?? '');
  const woodSpeciesId = ref(initial?.woodSpeciesId ?? '');
  const inoculate = ref(false);
  const hasTriedToSubmit = ref(false);

  const inoculationForm = useInoculationForm({
    // Only the logs' ids tie an inoculation to a log: a batch's logs do not exist yet.
    mushroomLogId: '',
    initialStrainId: options.initialStrainId,
    now: options.now,
  });

  const startNumberValue = computed(() => parseNumberInput(startNumber.value));
  const countValue = computed(() => parseNumberInput(count.value));
  const digitsValue = computed(() => parseNumberInput(digits.value));

  const numberingIssues = computed(() =>
    validateMushroomLogBatchInput({
      startNumber: startNumberValue.value,
      count: countValue.value,
      digits: digitsValue.value,
    }),
  );

  const issues = computed(() => [
    ...(zoneId.value === '' ? [{ field: 'zone' as const, code: INPUT_ISSUE_CODE.Required }] : []),
    ...numberingIssues.value,
  ]);

  const labels = computed(() =>
    numberingIssues.value.length > 0
      ? []
      : mushroomLogBatchLabels({
          prefix: prefix.value,
          startNumber: startNumberValue.value!,
          count: countValue.value!,
          digits: digitsValue.value!,
        }),
  );

  function issue(field: MushroomLogBatchFormField): InputIssueCode | undefined {
    return hasTriedToSubmit.value
      ? issues.value.find((candidate) => candidate.field === field)?.code
      : undefined;
  }

  function inoculationIssue(field: InoculationFormField): InputIssueCode | undefined {
    return inoculate.value ? inoculationForm.issue(field) : undefined;
  }

  function submit(): MushroomLogBatchInput | undefined {
    hasTriedToSubmit.value = true;

    // Both are checked, so that a form wrong in two places shows both at once.
    const inoculationInput = inoculate.value ? inoculationForm.submit() : undefined;

    if (issues.value.length > 0 || (inoculate.value && !inoculationInput)) {
      return undefined;
    }

    return {
      prefix: prefix.value,
      startNumber: startNumberValue.value!,
      count: countValue.value!,
      digits: digitsValue.value!,
      zone: { id: zoneId.value },
      ...(woodSpeciesId.value === '' ? {} : { woodSpecies: { id: woodSpeciesId.value } }),
      ...(inoculationInput ? { inoculation: withoutLog(inoculationInput) } : {}),
    };
  }

  function reset(): void {
    prefix.value = initial?.prefix ?? '';
    startNumber.value = initialStart();
    count.value = '';
    digits.value = initialDigits();
    zoneId.value = initial?.zoneId ?? '';
    woodSpeciesId.value = initial?.woodSpeciesId ?? '';
    inoculate.value = false;
    inoculationForm.reset();
    hasTriedToSubmit.value = false;
  }

  return {
    prefix,
    startNumber,
    count,
    digits,
    zoneId,
    woodSpeciesId,
    inoculate,
    inoculation: {
      strainId: inoculationForm.strainId,
      method: inoculationForm.method,
      inoculatedAt: inoculationForm.inoculatedAt,
      quantity: inoculationForm.quantity,
      maxDay: inoculationForm.maxDay,
    },
    labels,
    issue,
    inoculationIssue,
    submit,
    reset,
  };
}
