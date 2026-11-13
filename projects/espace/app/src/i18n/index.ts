import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import enUs from './en-US';
import frFr from './fr-FR';

export const MESSAGES = {
  'fr-FR': frFr,
  'en-US': enUs,
  'en-GB': enUs,
} as const;

export const TRANSLATION_KEY = 'espace_app';

/** Texts shared by several screens: navigation, activities, list states, filters… */
export function useAppI18n() {
  return useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });
}
