import type { InoculationMethod } from '@lychen/typescript-front-model-business/mushroom/Inoculation';
import type { JournalEntryKind } from '@lychen/typescript-front-model-business/mushroom/JournalEntry';
import type { MushroomLogState } from '@lychen/typescript-front-model-business/mushroom/MushroomLog';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import { TRANSLATION_KEY, messages } from './i18n';

export interface UseMushroomLabels {
  /** "En incubation"… An empty string for a state this front does not know (undefined). */
  mushroomLogStateLabel: (state?: MushroomLogState) => string;
  /** "Trempage"… An empty string for a kind this front does not know (undefined). */
  journalEntryKindLabel: (kind?: JournalEntryKind) => string;
  /** "Chevilles"… An empty string for a method this front does not know (undefined). */
  inoculationMethodLabel: (method?: InoculationMethod) => string;
}

/**
 * The names of the model's states, journal kinds and inoculation methods, in the current
 * language. The mappers give `undefined` for a value the front does not know yet (a newer API),
 * and so does a label here: a screen then shows nothing rather than a raw key.
 */
export function useMushroomLabels(): UseMushroomLabels {
  const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

  return {
    mushroomLogStateLabel: (state) => (state ? t(`state.${state}`) : ''),
    journalEntryKindLabel: (kind) => (kind ? t(`journal_entry_kind.${kind}`) : ''),
    inoculationMethodLabel: (method) => (method ? t(`inoculation_method.${method}`) : ''),
  };
}
