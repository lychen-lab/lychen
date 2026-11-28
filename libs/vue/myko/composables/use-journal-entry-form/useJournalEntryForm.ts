import { computed, ref, toValue, type MaybeRefOrGetter, type Ref } from 'vue';

import {
  JOURNAL_ENTRY_KIND,
  type JournalEntryInput,
} from '@lychen/typescript-front-model-business/mushroom/JournalEntry';
import {
  INPUT_ISSUE_CODE,
  validateJournalEntryInput,
  type InputIssueCode,
} from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';
import {
  fromDateTimeInputValue,
  toDateTimeInputValue,
} from '@lychen/typescript-myko/form/dayInput';

export type JournalEntryFormKind = JournalEntryInput['kind'];

/**
 * The kinds a person can write, in the order the chips show them: the routine gestures first, then
 * what is written down. `state_change` is not here: only the API writes it.
 */
export const JOURNAL_ENTRY_FORM_KINDS: readonly JournalEntryFormKind[] = [
  JOURNAL_ENTRY_KIND.Soaking,
  JOURNAL_ENTRY_KIND.Watering,
  JOURNAL_ENTRY_KIND.Moving,
  JOURNAL_ENTRY_KIND.Treatment,
  JOURNAL_ENTRY_KIND.Observation,
  JOURNAL_ENTRY_KIND.Note,
  JOURNAL_ENTRY_KIND.Other,
];

export type JournalEntryFormField = 'kind' | 'text';

export interface UseJournalEntryFormOptions {
  /** The log the entry is written on. */
  mushroomLogId: MaybeRefOrGetter<string>;
  /** A kind chosen when the form opens (a bulk entry of soakings, say). None by default. */
  initialKind?: JournalEntryFormKind;
  /** The clock: the moment the form starts on. Tests give their own. */
  now?: () => Date;
}

export interface UseJournalEntryForm {
  /** The chosen kind: none until a chip is tapped, unless `initialKind` says otherwise. */
  kind: Ref<JournalEntryFormKind | undefined>;
  text: Ref<string>;
  /** The date and time field's value (`2026-03-12T08:05`): now until changed. */
  occurredAt: Ref<string>;
  /** Whether the text is required for the chosen kind (a note, an observation). */
  isTextRequired: Readonly<Ref<boolean>>;
  /** What is wrong with a field, once a submission has been tried (see `useHarvestForm`). */
  issue: (field: JournalEntryFormField) => InputIssueCode | undefined;
  /** The input to send, or undefined (and the issues shown) when something is wrong. */
  submit: () => JournalEntryInput | undefined;
  /** Back to no kind (or the initial one), no text and now, as when the sheet opens again. */
  reset: () => void;
}

/**
 * The state of the journal form ("noter un trempage"): the kind chosen from chips, the text and
 * the moment. The text rules are `validateJournalEntryInput`'s, the API's own. A blank or unreadable
 * moment is left out of the input: the API then uses the current time.
 */
export function useJournalEntryForm(options: UseJournalEntryFormOptions): UseJournalEntryForm {
  const now = options.now ?? (() => new Date());

  const kind = ref<JournalEntryFormKind | undefined>(options.initialKind);
  const text = ref('');
  const occurredAt = ref(toDateTimeInputValue(now()));
  const hasTriedToSubmit = ref(false);

  // Asking the API's own rule with an empty text: it refuses one only for the kinds that need it.
  const isTextRequired = computed(
    () => validateJournalEntryInput({ kind: kind.value, text: '' }).length > 0,
  );

  const issues = computed(() => [
    ...(kind.value === undefined
      ? [{ field: 'kind' as const, code: INPUT_ISSUE_CODE.Required }]
      : []),
    ...validateJournalEntryInput({ kind: kind.value, text: text.value }),
  ]);

  function issue(field: JournalEntryFormField): InputIssueCode | undefined {
    return hasTriedToSubmit.value
      ? issues.value.find((candidate) => candidate.field === field)?.code
      : undefined;
  }

  function submit(): JournalEntryInput | undefined {
    hasTriedToSubmit.value = true;

    if (issues.value.length > 0 || kind.value === undefined) {
      return undefined;
    }

    const trimmedText = text.value.trim();
    const moment = fromDateTimeInputValue(occurredAt.value);

    return {
      mushroomLog: { id: toValue(options.mushroomLogId) },
      kind: kind.value,
      ...(moment ? { occurredAt: moment } : {}),
      ...(trimmedText === '' ? {} : { text: trimmedText }),
    };
  }

  function reset(): void {
    kind.value = options.initialKind;
    text.value = '';
    occurredAt.value = toDateTimeInputValue(now());
    hasTriedToSubmit.value = false;
  }

  return { kind, text, occurredAt, isTextRequired, issue, submit, reset };
}
