import type { LocaleMessages, VueMessageType } from 'vue-i18n';

export type LocaleMap = Record<string, unknown>;

export function buildMessagesFromModules(modules: Record<string, unknown>) {
  const messages: Record<string, LocaleMessages<VueMessageType>> = {};

  Object.entries(modules).forEach(([path, mod]) => {
    const key = path.replace(/^\.\/|\.json$/g, '');
    const localeMessages = (mod as { default: LocaleMessages<VueMessageType> }).default ?? mod;

    messages[key] = localeMessages ?? {};
  });

  /** Extra mapping */
  if (messages['en-US']) {
    messages['en-GB'] = messages['en-US'];
    messages['en-CA'] = messages['en-US'];
    messages['en'] = messages['en-US'];
  }

  if (messages['fr-FR']) {
    messages['fr'] = messages['fr-FR'];
  }

  return messages;
}
